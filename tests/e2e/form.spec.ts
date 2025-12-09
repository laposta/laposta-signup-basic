import { expect, test } from '@playwright/test';
import {
  API_KEY,
  createPostViaUi,
  deletePage,
  fillRequiredFields,
  getRestNonce,
  resolveListId,
  submitForm,
} from './helpers';

test('submit form inside a UI-created post (happy path + validation)', async ({ page }) => {
  test.skip(!API_KEY, 'LSB_API_KEY must be set to run this test.');

  const listId = await resolveListId(page);
  const shortcode = `[laposta_signup_basic_form list_id="${listId}"]`;

  const restNonce = await getRestNonce(page);
  const { viewUrl, postId } = await createPostViaUi(page, shortcode);

  try {
    await page.goto(viewUrl, { waitUntil: 'networkidle' });
    const form = page.locator('.js-lsb-form');
    const globalError = page.locator('.lsb-form-global-error');

    if (await globalError.count()) {
      const errorText = (await globalError.first().innerText()).trim();
      if (/API request failed|API-key provided|Unknown error/i.test(errorText)) {
        test.skip(`Laposta API not available: "${errorText}"`);
      }
    }

    await expect(form).toBeVisible();

    await fillRequiredFields(form, { includeEmail: false });
    await submitForm(form);
    const validationError = form.locator('.lsb-form-global-error');
    await expect(validationError).toBeVisible();
    await expect(validationError).not.toHaveText('');

    await fillRequiredFields(form, { includeEmail: true });
    await submitForm(form);

    const success = form.locator('.lsb-form-success-container');
    await expect(success).toBeVisible();
    await expect(success).not.toHaveText('');
  } finally {
    await deletePage(page, restNonce, postId);
  }
});
