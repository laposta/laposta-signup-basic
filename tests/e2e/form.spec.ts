import { expect, Locator, test } from '@playwright/test';
import {
  API_KEY,
  createPostViaUi,
  deletePage,
  fillRequiredFields,
  getRestNonce,
  resolveListId,
  submitForm,
} from './helpers';

const FORM_NAME = /(Newsletter signup form|Nieuwsbrief aanmeldformulier)/i;
const HONEYPOT_NAME = /Your work e-mail here/i;

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

    const accessibleForm = page.getByRole('form', { name: FORM_NAME });
    await expect(accessibleForm).toBeVisible();
    await expect(form).toBeVisible();
    await expectSubmitButtonToBeAccessibleByVisibleName(form);
    await expect(form.getByRole('textbox', { name: HONEYPOT_NAME })).toHaveCount(0);
    await expectVisibleLabelsToResolveToAccessibleControls(form);

    await fillRequiredFields(form, { includeEmail: false });
    await submitForm(form);
    const validationError = form.locator('.lsb-form-global-error');
    await expect(validationError).toBeVisible();
    await expect(validationError).not.toHaveText('');
    await expectFirstEmailFieldToBeFocused(form);

    await fillRequiredFields(form, { includeEmail: true });
    await submitForm(form);

    const success = form.locator('.lsb-form-success-container');
    await expect(success).toBeVisible();
    await expect(success).not.toHaveText('');
  } finally {
    await deletePage(page, restNonce, postId);
  }
});

async function expectVisibleLabelsToResolveToAccessibleControls(form: Locator) {
  const labelTexts = await getVisibleText(form.locator('label:visible'));
  expect(labelTexts.length).toBeGreaterThan(0);

  for (const labelText of labelTexts) {
    await expect(form.getByLabel(namePattern(labelText)).first()).toBeVisible();
  }

  const legendTexts = await getVisibleText(form.locator('legend:visible'));
  for (const legendText of legendTexts) {
    await expect(form.getByRole('group', { name: namePattern(legendText) })).toBeVisible();
  }
}

async function expectFirstEmailFieldToBeFocused(form: Locator) {
  const emailField = form.locator('.lsb-field-type-email').first();
  const emailFieldName = (await emailField.locator('.lsb-form-label-name').first().innerText()).trim();
  await expect(form.getByLabel(emailFieldName).first()).toBeFocused();
}

async function expectSubmitButtonToBeAccessibleByVisibleName(form: Locator) {
  const submitButtonText = (await form.locator('button[name="lsb_form_submit"]').innerText()).trim();
  await expect(form.getByRole('button', { name: new RegExp(escapeRegExp(submitButtonText), 'i') })).toBeVisible();
}

function escapeRegExp(value: string) {
  return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function namePattern(value: string) {
  return new RegExp(value.trim().split(/\s+/).map(escapeRegExp).join('\\s+'), 'i');
}

async function getVisibleText(locator: Locator) {
  return locator.evaluateAll((elements) =>
    elements
      .map((element) => element.textContent?.replace(/\s+/g, ' ').trim() || '')
      .filter(Boolean),
  );
}
