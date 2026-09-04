import { expect, Page, Route, test } from '@playwright/test';
import path from 'node:path';
import { submitForm } from './helpers';

const MAIN_SCRIPT = path.resolve('assets/js/lsb-form/main.js');
const TOKEN_URL = 'https://spam.example.test/token.php?a=account&l=list&p=1';
const POW_URL = 'https://spam.example.test/pow.js';
const SUCCESS_MESSAGE = 'Subscription confirmed';
const ERROR_MESSAGE = 'Submission failed safely';

type FormspamFixtureOptions = {
  tokenRequest?: 'success' | 'abort';
  solver: 'success' | 'throw' | 'silent';
  ajaxResponse?: 'success' | 'error';
  removeProofFields?: boolean;
};

test('serializes a successful browser token and proof into the real form submission', async ({ page }) => {
  const ajaxBodies = await installFormspamFixture(page, { solver: 'success' });

  await submitForm(page.locator('.js-lsb-form'));
  await expect(page.locator('.lsb-form-success-container')).toHaveText(SUCCESS_MESSAGE);

  expect(ajaxBodies).toHaveLength(1);
  expect(ajaxBodies[0]).toContain('subscribe_token');
  expect(ajaxBodies[0]).toContain(encodeURIComponent('signed-token'));
  expect(ajaxBodies[0]).toContain('subscribe_pow');
  expect(ajaxBodies[0]).toContain(encodeURIComponent('42'));
});

test('continues to WordPress AJAX when the token request is aborted', async ({ page }) => {
  const ajaxBodies = await installFormspamFixture(page, {
    tokenRequest: 'abort',
    solver: 'success',
  });

  await submitForm(page.locator('.js-lsb-form'));
  await expect(page.locator('.lsb-form-success-container')).toHaveText(SUCCESS_MESSAGE);

  expect(ajaxBodies).toHaveLength(1);
});

test('submits normally when browser proof fields have been removed', async ({ page }) => {
  const ajaxBodies = await installFormspamFixture(page, {
    solver: 'success',
    removeProofFields: true,
  });

  await submitForm(page.locator('.js-lsb-form'));
  await expect(page.locator('.lsb-form-success-container')).toHaveText(SUCCESS_MESSAGE);

  expect(ajaxBodies).toHaveLength(1);
});

test('recovers when the proof solver throws', async ({ page }) => {
  const ajaxBodies = await installFormspamFixture(page, {
    solver: 'throw',
    ajaxResponse: 'error',
  });
  const submitButton = page.locator('button[name="lsb_form_submit"]');

  await submitForm(page.locator('.js-lsb-form'));
  await expect(page.locator('.lsb-form-global-error')).toHaveText(ERROR_MESSAGE, {
    timeout: 15_000,
  });

  expect(ajaxBodies).toHaveLength(1);
  await expect(submitButton).toBeEnabled();
});

test('recovers when the proof solver never calls back', async ({ page }) => {
  const ajaxBodies = await installFormspamFixture(page, {
    solver: 'silent',
    ajaxResponse: 'error',
  });
  const submitButton = page.locator('button[name="lsb_form_submit"]');

  await submitForm(page.locator('.js-lsb-form'));
  await expect(page.locator('.lsb-form-global-error')).toHaveText(ERROR_MESSAGE, {
    timeout: 15_000,
  });

  expect(ajaxBodies).toHaveLength(1);
  await expect(submitButton).toBeEnabled();
});

async function installFormspamFixture(page: Page, options: FormspamFixtureOptions) {
  const ajaxBodies: string[] = [];

  await page.route('**/token.php**', async (route) => {
    if (options.tokenRequest === 'abort') {
      await route.abort('failed');
      return;
    }

    await route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({
        token: 'signed-token',
        challenge: { salt: 'salt', difficulty: 1 },
      }),
    });
  });
  await page.route('**/pow.js', (route) => fulfillSolver(route, options.solver));
  await page.route('**/wp-admin/admin-ajax.php**', async (route) => {
    ajaxBodies.push(route.request().postData() || '');
    const success = options.ajaxResponse !== 'error';
    await route.fulfill({
      contentType: 'application/json',
      body: JSON.stringify({
        status: success ? 'success' : 'error',
        html: success ? SUCCESS_MESSAGE : ERROR_MESSAGE,
      }),
    });
  });

  await page.goto('/');
  await page.setContent(formMarkup());
  await page.addScriptTag({
    url: new URL('/wp-includes/js/jquery/jquery.min.js', page.url()).toString(),
  });
  await page.evaluate(() => {
    (window as typeof window & { lsbConfig: object }).lsbConfig = {
      class: {
        fieldHasErrorClass: 'has-field-error',
        inputHasErrorClass: 'has-input-error',
      },
      trans: {
        'global.form_contains_errors': 'Form contains errors',
        'global.loading': 'Submitting',
        'global.unknown_error': 'Unknown error',
      },
    };
  });
  await page.addScriptTag({ path: MAIN_SCRIPT });
  await page.waitForFunction(() =>
    Boolean((window as typeof window & { LapostaPow?: object }).LapostaPow),
  );

  if (options.removeProofFields) {
    await page.locator('.js-token-input, .js-pow-input').evaluateAll((elements) => {
      elements.forEach((element) => element.remove());
    });
  }

  return ajaxBodies;
}

async function fulfillSolver(route: Route, solver: FormspamFixtureOptions['solver']) {
  const solveBody = {
    success: "callback('42');",
    throw: "throw new Error('solver failed');",
    silent: '',
  }[solver];

  await route.fulfill({
    contentType: 'application/javascript',
    body: `window.LapostaPow = {
      solve: function (salt, difficulty, callback) { ${solveBody} }
    };`,
  });
}

function formMarkup() {
  return `<!doctype html>
    <html>
      <head>
        <style>.lsb-visually-hidden { display: none; }</style>
      </head>
      <body>
        <form
          class="js-lsb-form"
          data-form-post-url="/wp-admin/admin-ajax.php?action=laposta_signup_basic&route=form_submit"
          data-token-url="${TOKEN_URL}"
          data-pow-url="${POW_URL}"
        >
          <div class="lsb-form-body">
            <input type="hidden" name="laposta_signup_basic[subscribe_token]" class="js-token-input">
            <input type="hidden" name="laposta_signup_basic[subscribe_pow]" class="js-pow-input">
            <button type="submit" name="lsb_form_submit">Submit</button>
            <span class="lsb-loader" hidden></span>
            <span class="lsb-loader-aria"></span>
            <div class="lsb-form-global-error lsb-visually-hidden"></div>
          </div>
          <div class="lsb-form-success-container lsb-visually-hidden"></div>
        </form>
      </body>
    </html>`;
}
