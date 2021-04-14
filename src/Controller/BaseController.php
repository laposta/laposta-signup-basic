<?php

namespace Laposta\SignupBasic\Controller;

abstract class BaseController
{
    /**
     * @return string
     */
    abstract protected function getTemplateDir();

    protected function showTemplate($path, array $vars = null)
    {
        if (is_array($vars)) {
            extract($vars);
        }

        include $this->getTemplateDir().$path;
    }

    protected function getRenderedTemplate($path, array $vars = null)
    {
        ob_start();
        $this->showTemplate($path, $vars);
        return ob_get_clean();
    }

    /**
     * @return null|\WP_User
     */
    public function getCurrentUser()
    {
        return wp_get_current_user()->ID ? wp_get_current_user() : null;
    }

    /**
     * @return int|null
     */
    public function getCurrentUserId()
    {
        return $this->getCurrentUser() ? $this->getCurrentUser()->ID : null;
    }

    /**
     * @param null $user
     *
     * @return bool
     */
    public function isAdministrator($user = null)
    {
        return $this->userHasRole('administrator', $user);
    }

    /**
     * @param $role
     * @param null | \WP_User $user
     *
     * @return bool
     */
    public function userHasRole($role, $user = null)
    {
        $user = $user ?: $this->getCurrentUser();
        if (!$user) {
            return false;
        }

        return (in_array($role, $this->getCurrentUser()->roles));
    }

    public function sanitizeData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeData($value);
                continue;
            }
            $data[$key] = sanitize_text_field($value);
        }

        return $data;
    }

}