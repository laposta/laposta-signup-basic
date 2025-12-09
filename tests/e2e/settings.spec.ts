import { expect, test } from '@playwright/test';
import { API_KEY, updateApiKey, expectGreaterThan } from './helpers';

test.describe('Settings', () => {

  test('settings page renders and exposes API key input', async ({ page }) => {
    await page.goto('/wp-admin/options-general.php?page=laposta_signup_basic_settings');

    await expect(page.locator('h1:has-text("Laposta Signup Basic")')).toBeVisible();
    await expect(page.locator(`input[name="laposta-api_key"]`)).toBeVisible();
    await expect(page.locator('.js-reset-cache')).toBeVisible();
  });

  test('cache reset button works', async ({ page }) => {
    await page.goto('/wp-admin/options-general.php?page=laposta_signup_basic_settings');
    await expect(page.locator('.js-reset-result-success')).toBeHidden();
    await expect(page.locator('.js-reset-result-error')).toBeHidden();

    await page.locator('.js-reset-cache').click();
    await expect(page.locator('.js-reset-result-success')).toBeVisible();
  });

  test('invalid API key shows an error, valid key restores lists', async ({ page }) => {
    test.skip(!API_KEY, 'LSB_API_KEY must be set to run this test.');

    await page.goto('/wp-admin/options-general.php?page=laposta_signup_basic_settings');
    const invalidKey = `invalid-${Date.now()}`;

    await updateApiKey(page, invalidKey);
    await expect(page.locator('.lsb-settings__error')).toBeVisible();
    await expect(page.locator('.lsb-settings__error')).not.toHaveText('');

    await updateApiKey(page, API_KEY);
    await expect(page.locator('.lsb-settings__error')).toBeHidden();
    const listLinks = page.locator('.lsb-settings__lists .js-list');
    await expectGreaterThan(listLinks, 0);
  });
});
