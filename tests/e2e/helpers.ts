import { expect, Locator, Page } from '@playwright/test';

export const SHORTCODE = '[laposta_signup_basic_form]';
export const TEST_LIST_ID = process.env.LSB_TEST_LIST_ID || process.env.WP_TEST_LIST_ID;
export const API_KEY = process.env.LSB_API_KEY;

export async function getRestNonce(page: Page): Promise<string> {
  await page.goto('/wp-admin/post-new.php?post_type=page');

  const restNonce = await page.evaluate(() => {
    const win = window as typeof window & {
      wpApiSettings?: { nonce?: string };
      wp?: { apiFetch?: { nonceMiddleware?: { nonce?: string } } };
    };
    return (
      win.wpApiSettings?.nonce ||
      win.wp?.apiFetch?.nonceMiddleware?.nonce ||
      document.querySelector('meta[name="api-nonce"]')?.getAttribute('content')
    );
  });

  if (!restNonce) {
    throw new Error('Could not read wpApiSettings.nonce; ensure the test user can access wp-admin.');
  }

  return restNonce;
}

export async function createPageWithContent(page: Page, restNonce: string, title: string, content: string) {
  const response = await page.request.post('/wp-json/wp/v2/pages', {
    headers: { 'X-WP-Nonce': restNonce },
    data: {
      title,
      content,
      status: 'publish',
    },
  });

  expect(response.ok()).toBeTruthy();
  const body = await response.json();
  return {
    id: body.id as number,
    link: body.link as string,
  };
}

export async function deletePage(page: Page, restNonce: string, pageId: number) {
  await page.request.delete(`/wp-json/wp/v2/pages/${pageId}?force=true`, {
    headers: { 'X-WP-Nonce': restNonce },
  });
}

export async function dismissWelcomeGuide(page: Page) {
  await page.evaluate(() => {
    window.localStorage.setItem('wpcom_block_editor_welcome_guide_hidden', 'true');
    window.sessionStorage.setItem('wpcom_block_editor_welcome_guide_hidden', 'true');
  });

  const closeWelcome = page.getByRole('button', { name: /Close dialog/i });
  if ((await closeWelcome.count()) && (await closeWelcome.isVisible().catch(() => false))) {
    await closeWelcome.click();
  }
}

export async function fillRequiredFields(form: Locator, options: { includeEmail: boolean }) {
  const fields = form.locator('.lsb-form-field-wrapper');
  const count = await fields.count();

  for (let i = 0; i < count; i += 1) {
    const field = fields.nth(i);
    const required = (await field.getAttribute('data-required')) === 'true';
    if (!required) continue;

    const fieldType = await field.getAttribute('data-field-type');
    if (fieldType === 'email' && !options.includeEmail) {
      continue;
    }

    await fillField(field, fieldType || 'text');
  }
}

async function fillField(field: Locator, fieldType: string) {
  switch (fieldType) {
    case 'select': {
      const select = field.locator('select');
      const options = select.locator('option:not([value=""])');
      if ((await select.count()) && (await options.count())) {
        await select.selectOption({ index: 1 });
      }
      break;
    }
    case 'radio': {
      const radio = field.locator('input[type="radio"]').first();
      await radio.check();
      break;
    }
    case 'checkbox': {
      const checkbox = field.locator('input[type="checkbox"]').first();
      await checkbox.check();
      break;
    }
    case 'date': {
      const input = field.locator('input[type="date"]');
      await input.fill('2020-01-01');
      break;
    }
    case 'number': {
      const input = field.locator('input[type="number"]');
      await input.fill('42');
      break;
    }
    case 'email': {
      const input = field.locator('input[type="email"]');
      await input.fill(`playwright+${Date.now()}@example.com`);
      break;
    }
    default: {
      const input = field.locator('input:not([type="checkbox"]):not([type="radio"])').first();
      if (await input.count()) {
        await input.fill('Test value');
      } else {
        const textArea = field.locator('textarea');
        await textArea.fill('Test value');
      }
    }
  }
}

export async function submitForm(form: Locator) {
  const submit = form.locator('button[name="lsb_form_submit"]');
  await submit.click();
}

export async function updateApiKey(page: Page, apiKey: string) {
  const input = page.locator('input[name="laposta-api_key"]');
  await input.fill(apiKey);
  const saveButton = page.getByRole('button', { name: /(Save Changes|Wijzigingen opslaan)/i });
  await Promise.all([
    page.waitForURL(/options-general\.php\?page=laposta_signup_basic_settings/, { timeout: 15000 }),
    saveButton.click(),
  ]);
  await page.locator('.notice-success').first().waitFor({ state: 'visible', timeout: 10000 }).catch(() => {});
}

export async function resolveListId(page: Page, preferredListId?: string) {
  await page.goto('/wp-admin/options-general.php?page=laposta_signup_basic_settings');
  if (API_KEY) {
    await updateApiKey(page, API_KEY);
  }
  const listLinks = page.locator('.lsb-settings__lists .js-list');
  const count = await listLinks.count();
  if (!count) {
    throw new Error('No Laposta lists available for the current API key.');
  }

  const ids = await listLinks.evaluateAll((els) => els.map((el) => el.getAttribute('data-list-id')));

  if (preferredListId && ids.includes(preferredListId)) {
    return preferredListId;
  }

  const firstId = ids.find(Boolean);
  if (!firstId) {
    throw new Error('Could not read list_id from the settings page.');
  }

  return firstId;
}

export async function createPostViaUi(page: Page, shortcode: string) {
  await page.goto('/wp-admin/post-new.php');
  await dismissWelcomeGuide(page);

  const canvas = page.frameLocator('iframe[name="editor-canvas"]');

  const title = `Laposta form e2e ${Date.now()}`;
  const titleBox = canvas.getByRole('textbox', { name: /titel toevoegen/i });
  await expect(titleBox).toBeVisible();
  await titleBox.click();
  await titleBox.fill(title);

  await page.evaluate((sc) => {
    const wpAny = (window as typeof window & { wp: any }).wp;
    const { createBlock } = wpAny.blocks;
    const { dispatch } = wpAny.data;
    dispatch('core/block-editor').insertBlocks([createBlock('core/shortcode', { text: sc })]);
  }, shortcode);

  const content = await page.evaluate(
    () => (window as typeof window & { wp: any }).wp.data.select('core/editor').getEditedPostContent(),
  );
  expect(content).toContain(shortcode);

  await page.evaluate(() => {
    const { dispatch } = (window as typeof window & { wp: any }).wp.data;
    dispatch('core/editor').editPost({ status: 'publish' });
    dispatch('core/editor').savePost();
  });

  await expect
    .poll(
      () =>
        page.evaluate(() => {
          const sel = (window as typeof window & { wp: any }).wp.data.select('core/editor');
          return sel.isSavingPost() || sel.isPublishingPost();
        }),
      { timeout: 20000, message: 'Waiting for post publish to complete' },
    )
    .toBeFalsy();

  const { postId, permalink } = await page.evaluate(() => {
    const sel = (window as typeof window & { wp: any }).wp.data.select('core/editor');
    return { postId: sel.getCurrentPostId(), permalink: sel.getPermalink() };
  });

  const viewUrl = permalink || `${process.env.WP_BASE_URL}/?p=${postId}`;

  const numericPostId = Number(postId);
  if (!numericPostId) {
    throw new Error('Failed to read post ID after publishing.');
  }

  return { viewUrl, postId: numericPostId };
}

export async function expectGreaterThan(locator: Locator, min: number) {
  const count = await locator.count();
  expect(count).toBeGreaterThan(min);
}
