<?php

use Laposta\SignupBasic\Plugin;

/**
 *
 * @var string $optionGroup
 * @var string $apiKey
 * @var array $lists
 * @var string $status
 * @var string $statusMessage
 * @var string $refreshCacheUrl
 * @var array $classTypes
 */

use Laposta\SignupBasic\Service\DataService;

?>

<div class="lsb-settings wrap" data-reset-cache-url="<?php echo esc_url($refreshCacheUrl) ?>">

    <h1><?php echo esc_html__('Laposta Signup Basic Settings', 'laposta-signup-basic') ?></h1>

    <form method="post" action="options.php" autocomplete="off">

        <?php @settings_fields($optionGroup); ?>
        <section>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="<?php echo Plugin::OPTION_API_KEY ?>"><?php echo esc_html__('API key', 'laposta-signup-basic') ?></label></th>
                    <td><input type="text" name="<?php echo Plugin::OPTION_API_KEY ?>" id="<?php echo Plugin::OPTION_API_KEY ?>" value="<?php echo esc_html($apiKey) ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('Have fields been changed or is something going wrong?', 'laposta-signup-basic') ?></th>
                    <td>
                        <a href="#" class="button button-primary js-reset-cache"><?php echo esc_html__('Reset cache', 'laposta-signup-basic') ?></a>
                        <span style="display: none;" class="lsb-settings__reset-cache-result js-reset-result-success"><?php echo esc_html__('The cache has been emptied', 'laposta-signup-basic') ?></span>
                        <span style="display: none;"  class="lsb-settings__reset-cache-result js-reset-result-error"><?php echo esc_html__('Something went wrong', 'laposta-signup-basic') ?></span>
                    </td>
                </tr>
            </table>
        </section>

        <?php if ($status && $status !== DataService::STATUS_OK): ?>
            <section class="lsb-settings__error">
                <h2 class="lsb-settings__error-title"><?php echo esc_html__('Error message', 'laposta-signup-basic') ?></h2>
                <p class="lsb-settings__error-text">
                    <?php echo esc_html__('Unfortunately, something went wrong. Check out this error message:', 'laposta-signup-basic') ?> <br>
                    <?php echo esc_html($statusMessage) ?>
                </p>
            </section>
        <?php endif; ?>


        <!-- note: the option input fields must be there, otherwise they will be made empty when saving just the api key, therfore display: none instead of not outputting at all -->
        <div <?php if ($status !== DataService::STATUS_OK): ?>style="display: none"<?php endif ?>>
            <section class="lsb-settings__lists">
                <h2 class="lsb-settings__lists-title"><?php echo esc_html__('Overview of your lists', 'laposta-signup-basic') ?></h2>
                <p class="lsb-settings__lists-text">
                    <?php echo esc_html__('The lists below are linked to the specified API key. It is possible to display these on every Wordpress page by using a shortcode.', 'laposta-signup-basic') ?>
                </p>
                <h4><?php echo esc_html__("Click on a list to see that list's shortcode.", 'laposta-signup-basic') ?></h4>
                <?php foreach ($lists as $list): ?>
                    <a class="lsb-settings__list js-list" href="#" data-list-id="<?php echo esc_attr($list['list_id']) ?>">
                        <?php echo esc_html($list['name']) ?>
                    </a>
                <?php endforeach ?>
                <code class="lsb-settings__lists-shortcode-example-wrapper laposta-code js-shortcode-example-wrapper" style="display: none">
                    <span class="lsb-settings__lists-shortcode-example js-shortcode-example">
                        [<?php echo Plugin::SHORTCODE_RENDER_FORM ?> list_id="<span class="js-shortcode-example-list-id"></span>"]
                    </span>
                    <a style="display: none" href="#" class="lsb-settings__copy-shortcode js-copy-shortcode">
                        <span class="lsb-settings__copy-shortcode-text js-copy-shortcode-text">📋 <?php echo esc_html__('Copy to clipboard', 'laposta-signup-basic') ?></span>
                        <span class="lsb-settings__copy-shortcode-success js-copy-shortcode-success" style="display: none">✓ <?php echo esc_html__('Copied!', 'laposta-signup-basic') ?></span>
                    </a>
                </code>
            </section>

            <section class="lsb-settings__class-types">
                <h2 class="lsb-settings__class-types-title"><?php echo esc_html__('What kind of styling do you want to use for the form fields?', 'laposta-signup-basic') ?></h2>
                <div class="lsb-settings__class-type-items">
                    <?php foreach ($classTypes as $key => $val): ?>
                    <?php $key = esc_attr($key) ?>
                        <div class="lsb-settings__class-type-item">
                            <input class="lsb-settings__class-type-input js-class-type-input"
                                   type="radio"
                                   name="<?php echo Plugin::OPTION_CLASS_TYPE ?>"
                                   id="class_type_<?php echo $key ?>"
                                   value="<?php echo $key ?>"
                                   <?php if (get_option(Plugin::OPTION_CLASS_TYPE) === $key): ?>checked="checked"<?php endif ?>
                            >
                            <label for="class_type_<?php echo $key ?>"><?php echo esc_html($val) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="lsb-settings__class-type-custom js-default-classes-info"
                    <?php if (
                        get_option(Plugin::OPTION_CLASS_TYPE) !== DataService::CLASS_TYPE_DEFAULT): ?>
                        style="display: none"
                    <?php endif ?>
                >
                    <h4><?php echo esc_html__('Explanation of our default', 'laposta-signup-basic') ?></h4>
                    <p><?php echo esc_html__('Use this option to load our default styling.', 'laposta-signup-basic') ?></p>
                </div>

                <div class="lsb-settings__class-type-custom js-external-classes-info"
                    <?php if (
                            get_option(Plugin::OPTION_CLASS_TYPE) !== DataService::CLASS_TYPE_BOOTSTRAP_V4 &&
                            get_option(Plugin::OPTION_CLASS_TYPE) !== DataService::CLASS_TYPE_BOOTSTRAP_V5): ?>
                        style="display: none"
                    <?php endif ?>
                >
                    <h4><?php echo esc_html__('Explanation of bootstrap', 'laposta-signup-basic') ?></h4>
                    <p><?php echo esc_html__('Use this option if bootstrap is used in your Wordpress theme. We will add the appropriate bootstrap classes. To avoid possible conflicts, we do not load the bootstrap stylesheets.', 'laposta-signup-basic') ?></p>
                </div>

                <div class="lsb-settings__class-type-custom js-custom-classes-info"
                    <?php if (
                        get_option(Plugin::OPTION_CLASS_TYPE) !== DataService::CLASS_TYPE_CUSTOM): ?>
                        style="display: none"
                    <?php endif ?>
                >
                    <h4><?php echo esc_html__('Explanation of the custom settings', 'laposta-signup-basic') ?></h4>
                    <p><?php echo esc_html__('This option does not load any css files. You are fully responsible for the layout.', 'laposta-signup-basic') ?></p>
                </div>

                <div class="lsb-settings__class-type-custom js-add-classes-section">
                    <h4><?php echo esc_html__('Do you want to add extra classes?', 'laposta-signup-basic') ?></h4>
                    <p><?php echo esc_html__("We already add classes to each element (see heading 'Explanation of the classes'). If you choose 'yes', you have the option to add additional classes.", 'laposta-signup-basic') ?></p>
                    <?php
                    $addClassesOptionVal = get_option(Plugin::OPTION_ADD_CLASSES, '');
                    $addClassesOptionVal = $addClassesOptionVal === '' ? '1' : $addClassesOptionVal; // if empty set to checked, best BC option
                    $noText = esc_html__('no', 'laposta-signup-basic');
                    $yesText = esc_html__('yes', 'laposta-signup-basic');
                    foreach (['0' => $noText, '1' => $yesText] as $key => $val): $key = (string)$key; ?>
                        <input class="lsb-settings__class-type-input js-add-classes-input"
                               type="radio"
                               name="<?php echo Plugin::OPTION_ADD_CLASSES ?>"
                               id="add_class_<?php echo $key ?>"
                               value="<?php echo $key ?>"
                               <?php if ($addClassesOptionVal === $key): ?>checked<?php endif ?>
                        >
                        <label for="add_class_<?php echo $key ?>"><?php echo esc_html($val) ?></label>
                    <?php endforeach ?>
                </div>

                <div class="lsb-settings__class-type-custom js-custom-classes-section"
                    <?php if ($addClassesOptionVal !== '1'): ?>
                        style="display: none"
                    <?php endif ?>
                >
                    <h4><?php echo esc_html__('Add extra classes', 'laposta-signup-basic') ?></h4>
                    <p><?php echo esc_html__('Below you can add css classes for each element. It is possible to add multiple classes by means of a space. For example:', 'laposta-signup-basic') ?><br>
                        my-class1 my-class2
                    </p>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_FORM ?>"><?php echo esc_html__('Form class', 'laposta-signup-basic') ?></label></th>
                            <td><input type="text"
                                       name="<?php echo Plugin::OPTION_CLASS_FORM ?>"
                                       id="<?php echo Plugin::OPTION_CLASS_FORM ?>"
                                       value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_FORM)) ?>"
                                       placeholder="form-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_FORM_BODY ?>"><?php echo esc_html__('Form body class', 'laposta-signup-basic') ?></label></th>
                            <td><input type="text"
                                       name="<?php echo Plugin::OPTION_CLASS_FORM_BODY ?>"
                                       id="<?php echo Plugin::OPTION_CLASS_FORM_BODY ?>"
                                       value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_FORM_BODY)) ?>"
                                       placeholder="form-body-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_FIELD_WRAPPER ?>"><?php echo esc_html__('Field wrapper class', 'laposta-signup-basic') ?></label></th>
                            <td><input type="text"
                                       name="<?php echo Plugin::OPTION_CLASS_FIELD_WRAPPER ?>"
                                       id="<?php echo Plugin::OPTION_CLASS_FIELD_WRAPPER ?>"
                                       value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_FIELD_WRAPPER)) ?>"
                                       placeholder="field-wrapper-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_FIELD_HAS_ERROR ?>"><?php echo esc_html__('Field has error class', 'laposta-signup-basic') ?></label></th>
                            <td><input type="text"
                                       name="<?php echo Plugin::OPTION_CLASS_FIELD_HAS_ERROR ?>"
                                       id="<?php echo Plugin::OPTION_CLASS_FIELD_HAS_ERROR ?>"
                                       value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_FIELD_HAS_ERROR)) ?>"
                                       placeholder="field-has-error-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_INPUT_HAS_ERROR ?>"><?php echo esc_html__('Input has error class', 'laposta-signup-basic') ?></label></th>
                            <td><input type="text"
                                       name="<?php echo Plugin::OPTION_CLASS_INPUT_HAS_ERROR ?>"
                                       id="<?php echo Plugin::OPTION_CLASS_INPUT_HAS_ERROR ?>"
                                       value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_INPUT_HAS_ERROR)) ?>"
                                       placeholder="input-has-error-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_FIELD_ERROR_FEEDBACK ?>"><?php echo esc_html__('Field error feedback class', 'laposta-signup-basic') ?></label></th>
                            <td><input type="text"
                                       name="<?php echo Plugin::OPTION_CLASS_FIELD_ERROR_FEEDBACK ?>"
                                       id="<?php echo Plugin::OPTION_CLASS_FIELD_ERROR_FEEDBACK ?>"
                                       value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_FIELD_ERROR_FEEDBACK)) ?>"
                                       placeholder="field-error-feedback-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_INPUT ?>"><?php echo esc_html__('Input class', 'laposta-signup-basic') ?></label></th>
                            <td><input type="text"
                                       name="<?php echo Plugin::OPTION_CLASS_INPUT ?>"
                                       id="<?php echo Plugin::OPTION_CLASS_INPUT ?>"
                                       value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_INPUT)) ?>"
                                       placeholder="input-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_LABEL ?>"><?php echo esc_html__('Label class', 'laposta-signup-basic') ?></label></th>
                            <td><input type="text"
                                       name="<?php echo Plugin::OPTION_CLASS_LABEL ?>"
                                       id="<?php echo Plugin::OPTION_CLASS_LABEL ?>"
                                       value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_LABEL)) ?>"
                                       placeholder="label-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_LABEL_NAME ?>"><?php echo esc_html__('Label name class', 'laposta-signup-basic') ?></label></th>
                            <td><input type="text"
                                       name="<?php echo Plugin::OPTION_CLASS_LABEL_NAME ?>"
                                       id="<?php echo Plugin::OPTION_CLASS_LABEL_NAME ?>"
                                       value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_LABEL_NAME)) ?>"
                                       placeholder="label-name-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_LABEL_REQUIRED ?>"><?php echo esc_html__('Label required class', 'laposta-signup-basic') ?></label></th>
                            <td><input type="text"
                                       name="<?php echo Plugin::OPTION_CLASS_LABEL_REQUIRED ?>"
                                       id="<?php echo Plugin::OPTION_CLASS_LABEL_REQUIRED ?>"
                                       value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_LABEL_REQUIRED)) ?>"
                                       placeholder="label-required-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_SELECT ?>"><?php echo esc_html__('Select class', 'laposta-signup-basic') ?></label></th>
                            <td><input type="text"
                                       name="<?php echo Plugin::OPTION_CLASS_SELECT ?>"
                                       id="<?php echo Plugin::OPTION_CLASS_SELECT ?>"
                                       value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_SELECT)) ?>"
                                       placeholder="select-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_CHECKS_WRAPPER ?>"><?php echo esc_html__('Wrapper class for collection of radio/checkboxes fields', 'laposta-signup-basic') ?></label></th>
                            <td><input type="text"
                                       name="<?php echo Plugin::OPTION_CLASS_CHECKS_WRAPPER ?>"
                                       id="<?php echo Plugin::OPTION_CLASS_CHECKS_WRAPPER ?>"
                                       value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_CHECKS_WRAPPER)) ?>"
                                       placeholder="checks-wrapper-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_CHECK_WRAPPER ?>"><?php echo esc_html__('Wrapper class for single radio/checkbox field', 'laposta-signup-basic') ?></label></th>
                            <td><input type="text"
                                       name="<?php echo Plugin::OPTION_CLASS_CHECK_WRAPPER ?>"
                                       id="<?php echo Plugin::OPTION_CLASS_CHECK_WRAPPER ?>"
                                       value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_CHECK_WRAPPER)) ?>"
                                       placeholder="check-wrapper-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_CHECK_INPUT ?>"><?php echo esc_html__('Radio/checkbox input class', 'laposta-signup-basic') ?></label></th>
                            <td><input type="text"
                                       name="<?php echo Plugin::OPTION_CLASS_CHECK_INPUT ?>"
                                       id="<?php echo Plugin::OPTION_CLASS_CHECK_INPUT ?>"
                                       value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_CHECK_INPUT)) ?>"
                                       placeholder="check-input-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_CHECK_LABEL ?>"><?php echo esc_html__('Radio/checkbox label class', 'laposta-signup-basic') ?></label></th>
                            <td><input type="text"
                                       name="<?php echo Plugin::OPTION_CLASS_CHECK_LABEL ?>"
                                       id="<?php echo Plugin::OPTION_CLASS_CHECK_LABEL ?>"
                                       value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_CHECK_LABEL)) ?>"
                                       placeholder="check-label-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_SUBMIT_BUTTON_AND_LOADER_WRAPPER ?>"><?php echo esc_html__('Submit button en loader wrapper class', 'laposta-signup-basic') ?></label></th>
                            <td><input
                                        type="text"
                                        name="<?php echo Plugin::OPTION_CLASS_SUBMIT_BUTTON_AND_LOADER_WRAPPER ?>"
                                        id="<?php echo Plugin::OPTION_CLASS_SUBMIT_BUTTON_AND_LOADER_WRAPPER ?>"
                                        value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_SUBMIT_BUTTON_AND_LOADER_WRAPPER)) ?>"
                                        placeholder="submit-button-and-loader-wrapper-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_SUBMIT_BUTTON ?>"><?php echo esc_html__('Button class', 'laposta-signup-basic') ?></label></th>
                            <td><input type="text"
                                       name="<?php echo Plugin::OPTION_CLASS_SUBMIT_BUTTON ?>"
                                       id="<?php echo Plugin::OPTION_CLASS_SUBMIT_BUTTON ?>"
                                       value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_SUBMIT_BUTTON)) ?>"
                                       placeholder="button-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_LOADER ?>"><?php echo esc_html__('Loader class', 'laposta-signup-basic') ?></label></th>
                            <td><input
                                        type="text"
                                        name="<?php echo Plugin::OPTION_CLASS_LOADER ?>"
                                        id="<?php echo Plugin::OPTION_CLASS_LOADER ?>"
                                        value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_LOADER)) ?>"
                                        placeholder="loader-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_GLOBAL_ERROR ?>"><?php echo esc_html__('Global error class', 'laposta-signup-basic') ?></label></th>
                            <td><input
                                        type="text"
                                        name="<?php echo Plugin::OPTION_CLASS_GLOBAL_ERROR ?>"
                                        id="<?php echo Plugin::OPTION_CLASS_GLOBAL_ERROR ?>"
                                        value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_GLOBAL_ERROR)) ?>"
                                        placeholder="form-global-error-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_SUCCESS_CONTAINER ?>"><?php echo esc_html__('Success container class', 'laposta-signup-basic') ?></label></th>
                            <td><input
                                        type="text"
                                        name="<?php echo Plugin::OPTION_CLASS_SUCCESS_CONTAINER ?>"
                                        id="<?php echo Plugin::OPTION_CLASS_SUCCESS_CONTAINER ?>"
                                        value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_SUCCESS_CONTAINER)) ?>"
                                        placeholder="form-success-container-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_SUCCESS_WRAPPER ?>"><?php echo esc_html__('Successfully subscribed wrapper class', 'laposta-signup-basic') ?></label></th>
                            <td><input
                                        type="text"
                                        name="<?php echo Plugin::OPTION_CLASS_SUCCESS_WRAPPER ?>"
                                        id="<?php echo Plugin::OPTION_CLASS_SUCCESS_WRAPPER ?>"
                                        value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_SUCCESS_WRAPPER)) ?>"
                                        placeholder="success-wrapper-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_SUCCESS_TITLE ?>"><?php echo esc_html__('Successfully subscribed title class', 'laposta-signup-basic') ?></label></th>
                            <td><input
                                        type="text"
                                        name="<?php echo Plugin::OPTION_CLASS_SUCCESS_TITLE ?>"
                                        id="<?php echo Plugin::OPTION_CLASS_SUCCESS_TITLE ?>"
                                        value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_SUCCESS_TITLE)) ?>"
                                        placeholder="success-title-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo Plugin::OPTION_CLASS_SUCCESS_TEXT ?>"><?php echo esc_html__('Successfully subscribed text class', 'laposta-signup-basic') ?></label></th>
                            <td><input
                                        type="text"
                                        name="<?php echo Plugin::OPTION_CLASS_SUCCESS_TEXT ?>"
                                        id="<?php echo Plugin::OPTION_CLASS_SUCCESS_TEXT ?>"
                                        value="<?php echo esc_attr(get_option(Plugin::OPTION_CLASS_SUCCESS_TEXT)) ?>"
                                        placeholder="success-text-class"
                                >
                            </td>
                        </tr>
                    </table>
                </div>
            </section>

            <section>
                <h2><?php echo esc_html__('Explanation of the classes', 'laposta-signup-basic') ?></h2>
                <p>
                    <?php echo esc_html__('Below you will find an overview of the basic classes we use, which are added by default:', 'laposta-signup-basic') ?>
                </p>
                <code class="laposta-code">
                    .lsb-form<br>
                    .lsb-form-body<br>
                    .lsb-form-field-wrapper<br>
                    .lsb-form-field-has-error<br>
                    .lsb-form-input-has-error<br>
                    .lsb-form-field-error-feedback<br>
                    .lsb-form-label<br>
                    .lsb-form-label-name<br>
                    .lsb-form-label-required<br>
                    .lsb-form-input<br>
                    .lsb-form-checks<br>
                    .lsb-form-check<br>
                    .lsb-form-check-input<br>
                    .lsb-form-check-label<br>
                    .lsb-button-and-loader-wrapper<br>
                    .lsb-form-button<br>
                    .lsb-loader<br>
                    .lsb-form-global-error<br>
                    .lsb-form-success-container<br>
                    .lsb-success<br>
                    .lsb-success-title<br>
                    .lsb-success-text
                </code>
                <p>
                    <?php echo esc_html__('The &lt;form&gt; tag gets additional classes:', 'laposta-signup-basic') ?><br>
                    <code class="laposta-code">&lt;form class="lsb-form lsb-list-id-[listId]"&gt;</code>
                    <?php echo esc_html__('Where [listId] is replaced by the id of the list.', 'laposta-signup-basic') ?>
                </p>
                <p><?php echo esc_html__('Furthermore, the field wrappers receive the relation variable (tag) of that specific field as an extra class, as well as the field type:', 'laposta-signup-basic') ?>
                    <code class="laposta-code">&lt;div class="lsb-field-tag-[tag] lsb-field-type-[fieldType] [misc-classes]"&gt;</code>
                    <?php echo esc_html__('Where [tag] is replaced by the relation variable (tag) of the list,', 'laposta-signup-basic') ?> <br>
                    <?php echo esc_html__('[fieldType] is replaced by the field type (text, email, number, date, select, radio, checkbox)', 'laposta-signup-basic') ?><br>
                    <?php echo esc_html__('and [misc-classes] is replaced by the class that belongs to the chosen format + our own class \"lsb-form-field-wrapper\" + optionally the extra added classes as set under the heading \"Add extra classes\".', 'laposta-signup-basic') ?>
                    <br>
                    <?php echo esc_html__('This combination of unique form and field properties allows you to influence each field separately using CSS.', 'laposta-signup-basic') ?>
                </p>

            </section>

            <section class="lsb-settings__inline-css">
                <h2><?php echo esc_html__('Inline CSS', 'laposta-signup-basic') ?></h2>
                <p><?php echo esc_html__("In this field you can enter your own CSS. The CSS will only be added inline on pages where the shortcode is added.", 'laposta-signup-basic') ?></p>
                <label class="lsb-settings__inline-css-label" for="<?php echo Plugin::OPTION_INLINE_CSS ?>"><?php echo esc_html__('Inline css input field', 'laposta-signup-basic') ?></label>
                <textarea
                    class="lsb-settings__inline-css-input"
                    name="<?php echo Plugin::OPTION_INLINE_CSS ?>"
                    id="<?php echo Plugin::OPTION_INLINE_CSS ?>"
                    placeholder=".lsb-list-id-a1uwtjapfg .lsb-form-label {
    font-weight: bold;
}

.lsb-form-field-wrapper.lsb-field-type-email .lsb-form-label {
    font-size: 1.2em;
}
"
                ><?php echo esc_html(get_option(Plugin::OPTION_INLINE_CSS, '')) ?></textarea>
            </section>

            <section class="lsb-settings__misc">
                <h2><?php echo esc_html__('Other settings', 'laposta-signup-basic') ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="<?php echo Plugin::OPTION_SUBMIT_BUTTON_TEXT ?>"><?php echo esc_html__('Submit button text', 'laposta-signup-basic') ?></label></th>
                        <td><input
                                    type="text"
                                    name="<?php echo Plugin::OPTION_SUBMIT_BUTTON_TEXT ?>"
                                    id="<?php echo Plugin::OPTION_SUBMIT_BUTTON_TEXT ?>"
                                    value="<?php echo esc_attr(get_option(Plugin::OPTION_SUBMIT_BUTTON_TEXT)) ?>"
                                    placeholder="<?php echo esc_html__('Subscribe', 'laposta-signup-basic') ?>"
                            >
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="<?php echo Plugin::OPTION_SUCCESS_TITLE ?>"><?php echo esc_html__('Successfully subscribed title', 'laposta-signup-basic') ?></label></th>
                        <td><input
                                    type="text"
                                    name="<?php echo Plugin::OPTION_SUCCESS_TITLE ?>"
                                    id="<?php echo Plugin::OPTION_SUCCESS_TITLE ?>"
                                    value="<?php echo esc_attr(get_option(Plugin::OPTION_SUCCESS_TITLE)) ?>"
                                    placeholder="<?php echo esc_html__('Successfully subscribed', 'laposta-signup-basic') ?>"
                            >
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="<?php echo Plugin::OPTION_SUCCESS_TEXT ?>"><?php echo esc_html__('Successfully subscribed text', 'laposta-signup-basic') ?></label></th>
                        <td><textarea
                                    name="<?php echo Plugin::OPTION_SUCCESS_TEXT ?>"
                                    id="<?php echo Plugin::OPTION_SUCCESS_TEXT ?>"
                                    placeholder="<?php echo esc_html__('You have been successfully subscribed.', 'laposta-signup-basic') ?>"
                                    rows="3"
                            ><?php echo esc_html(get_option(Plugin::OPTION_SUCCESS_TEXT)) ?></textarea>
                        </td>
                    </tr>
                </table>
            </section>
        </div>

        <?php @submit_button(); ?>

    </form>

</div>