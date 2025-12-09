import { expect, test } from '@playwright/test';
import { SHORTCODE, createPageWithContent, deletePage, getRestNonce } from './helpers';

test('shortcode without list_id shows a validation error', async ({ page }) => {
  const restNonce = await getRestNonce(page);
  const { id, link } = await createPageWithContent(
    page,
    restNonce,
    'Laposta shortcode smoke test',
    SHORTCODE,
  );

  try {
    await page.goto(link, { waitUntil: 'networkidle' });
    await expect(page.locator('.lsb-form-global-error')).toContainText(/list_id/i);
  } finally {
    await deletePage(page, restNonce, id);
  }
});
