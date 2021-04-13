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

<div class="lsb-settings wrap" data-reset-cache-url="<?= $refreshCacheUrl ?>">

    <h1>Laposta Signup Basic Instellingen</h1>

    <form method="post" action="options.php" autocomplete="off">

        <?php @settings_fields($optionGroup); ?>
        <section>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="<?= Plugin::OPTION_API_KEY ?>">API key</label></th>
                    <td><input type="text" name="<?= Plugin::OPTION_API_KEY ?>" id="<?= Plugin::OPTION_API_KEY ?>" value="<?= $apiKey ?>"></td>
                </tr>
                <tr>
                    <th scope="row">Zijn er velden aangepast of gaat er iets mis?</th>
                    <td><a href="#" class="button button-primary js-reset-cache">Reset Cache</a><span class="lsb-settings__reset-cache-result js-reset-result"</td>
                </tr>
            </table>
        </section>

        <?php if ($status !== DataService::STATUS_OK): ?>
            <section class="lsb-settings__error">
                <h2 class="lsb-settings__error-title">Foutmelding</h2>
                <p class="lsb-settings__error-text">
                    Helaas is er iets misgegaan. Bekijk deze foutmelding: <br>
                    <?= $statusMessage ?>
                </p>
            </section>
        <?php endif; ?>


        <!-- note: the option input fields must be there, otherwise they will be made empty when saving just the api key, therfore display: none instead of not outputting at all -->
        <div <?php if ($status !== DataService::STATUS_OK): ?>style="display: none"<?php endif ?>>
            <section class="lsb-settings__lists">
                <h2 class="lsb-settings__lists-title">Overzicht van uw lijsten</h2>
                <p class="lsb-settings__lists-text">
                    De onderstaande lijsten zijn gekoppeld aan de opgegeven API key.
                    Het is mogelijk deze op elke Wordpress pagina te tonen door het gebruik van een shortcode.
                </p>
                <h4>Klik op een lijst om de shortcode van die lijst te zien.</h4>
                <?php foreach ($lists as $list): ?>
                    <a class="lsb-settings__list js-list" href="#" data-list-id="<?= $list['list_id'] ?>">
                        <?= $list['name'] ?>
                    </a>
                <?php endforeach ?>
                <code class="laposta-code lsb-settings__lists-shortcode-example js-shortcode-example" style="display: none">
                    [<?= Plugin::SHORTCODE_RENDER_FORM ?> list_id="<span class="js-shortcode-example-list-id"></span>"]
                </code>
            </section>

            <section class="lsb-settings__class-types">
                <h2 class="lsb-settings__class-types-title">Wat voor styles wilt u hanteren voor de formuliervelden?</h2>
                <div class="lsb-settings__class-type-items">
                    <?php foreach ($classTypes as $key => $val): ?>
                        <div class="lsb-settings__class-type-item">
                            <input class="lsb-settings__class-type-input js-class-type-input"
                                   type="radio"
                                   name="<?= Plugin::OPTION_CLASS_TYPE ?>"
                                   id="class_type_<?= $key ?>"
                                   value="<?= $key ?>"
                                   <?php if (get_option(Plugin::OPTION_CLASS_TYPE) === $key): ?>checked="checked"<?php endif ?>
                            >
                            <label for="class_type_<?= $key ?>"><?= $val ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="lsb-settings__class-type-custom js-external-classes-section"
                    <?php if (
                            get_option(Plugin::OPTION_CLASS_TYPE) !== DataService::CLASS_TYPE_BOOTSTRAP_V4 &&
                            get_option(Plugin::OPTION_CLASS_TYPE) !== DataService::CLASS_TYPE_BOOTSTRAP_V5): ?>
                        style="display: none"
                    <?php endif ?>>
                    <h4>Uitleg bij bootstrap</h4>
                    <p>Gebruik deze optie als bootstrap is gebruikt in uw Wordpress theme. Om mogelijke conflicten te voorkomen laden wij deze styling niet in.</p>
                </div>
                <div class="lsb-settings__class-type-custom js-custom-classes-section"
                    <?php if (get_option(Plugin::OPTION_CLASS_TYPE) !== DataService::CLASS_TYPE_CUSTOM): ?>
                        style="display: none"
                    <?php endif ?>>
                    <h4>Handmatig instellen van classes</h4>
                    <p>Hieronder kunt u per element de css class bepalen. Het is mogelijk om meerdere classes toe te voegen door middel van een spatie. Bv:<br>
                        my-class1 my-class2
                    </p>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="<?= Plugin::OPTION_CLASS_FORM ?>">Form class</label></th>
                            <td><input type="text"
                                       name="<?= Plugin::OPTION_CLASS_FORM ?>"
                                       id="<?= Plugin::OPTION_CLASS_FORM ?>"
                                       value="<?= get_option(Plugin::OPTION_CLASS_FORM) ?>"
                                       placeholder="form-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?= Plugin::OPTION_CLASS_FIELD_WRAPPER ?>">Field wrapper class</label></th>
                            <td><input type="text"
                                       name="<?= Plugin::OPTION_CLASS_FIELD_WRAPPER ?>"
                                       id="<?= Plugin::OPTION_CLASS_FIELD_WRAPPER ?>"
                                       value="<?= get_option(Plugin::OPTION_CLASS_FIELD_WRAPPER) ?>"
                                       placeholder="field-wrapper-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?= Plugin::OPTION_CLASS_INPUT ?>">Input class</label></th>
                            <td><input type="text"
                                       name="<?= Plugin::OPTION_CLASS_INPUT ?>"
                                       id="<?= Plugin::OPTION_CLASS_INPUT ?>"
                                       value="<?= get_option(Plugin::OPTION_CLASS_INPUT) ?>"
                                       placeholder="input-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?= Plugin::OPTION_CLASS_LABEL ?>">Label class</label></th>
                            <td><input type="text"
                                       name="<?= Plugin::OPTION_CLASS_LABEL ?>"
                                       id="<?= Plugin::OPTION_CLASS_LABEL ?>"
                                       value="<?= get_option(Plugin::OPTION_CLASS_LABEL) ?>"
                                       placeholder="label-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?= Plugin::OPTION_CLASS_SELECT ?>">Select class</label></th>
                            <td><input type="text"
                                       name="<?= Plugin::OPTION_CLASS_SELECT ?>"
                                       id="<?= Plugin::OPTION_CLASS_SELECT ?>"
                                       value="<?= get_option(Plugin::OPTION_CLASS_SELECT) ?>"
                                       placeholder="select-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?= Plugin::OPTION_CLASS_CHECKS_WRAPPER ?>">Wrapper class for collection of radio/checkboxes fields</label></th>
                            <td><input type="text"
                                       name="<?= Plugin::OPTION_CLASS_CHECKS_WRAPPER ?>"
                                       id="<?= Plugin::OPTION_CLASS_CHECKS_WRAPPER ?>"
                                       value="<?= get_option(Plugin::OPTION_CLASS_CHECKS_WRAPPER) ?>"
                                       placeholder="checks-wrapper-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?= Plugin::OPTION_CLASS_CHECK_WRAPPER ?>">Wrapper class for single radio/checkbox field</label></th>
                            <td><input type="text"
                                       name="<?= Plugin::OPTION_CLASS_CHECK_WRAPPER ?>"
                                       id="<?= Plugin::OPTION_CLASS_CHECK_WRAPPER ?>"
                                       value="<?= get_option(Plugin::OPTION_CLASS_CHECK_WRAPPER) ?>"
                                       placeholder="check-wrapper-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?= Plugin::OPTION_CLASS_CHECK_INPUT ?>">Radio/checkbox input class</label></th>
                            <td><input type="text"
                                       name="<?= Plugin::OPTION_CLASS_CHECK_INPUT ?>"
                                       id="<?= Plugin::OPTION_CLASS_CHECK_INPUT ?>"
                                       value="<?= get_option(Plugin::OPTION_CLASS_CHECK_INPUT) ?>"
                                       placeholder="check-input-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?= Plugin::OPTION_CLASS_CHECK_LABEL ?>">Radio/checkbox label class</label></th>
                            <td><input type="text"
                                       name="<?= Plugin::OPTION_CLASS_CHECK_LABEL ?>"
                                       id="<?= Plugin::OPTION_CLASS_CHECK_LABEL ?>"
                                       value="<?= get_option(Plugin::OPTION_CLASS_CHECK_LABEL) ?>"
                                       placeholder="check-label-class"
                                >
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?= Plugin::OPTION_CLASS_SUBMIT_BUTTON ?>">Button class</label></th>
                            <td><input type="text"
                                       name="<?= Plugin::OPTION_CLASS_SUBMIT_BUTTON ?>"
                                       id="<?= Plugin::OPTION_CLASS_SUBMIT_BUTTON ?>"
                                       value="<?= get_option(Plugin::OPTION_CLASS_SUBMIT_BUTTON) ?>"
                                       placeholder="button-class"
                                >
                            </td>
                        </tr>

                    </table>
                </div>
            </section>

            <section>
                <h2>Uitleg over de classes</h2>
                <p>
                    Hieronder zit u een overzicht van de basisclasses die we gebruiken:
                </p>
                <code class="laposta-code">
                    .lsb-form<br>
                    .lsb-form-field-wrapper<br>
                    .lsb-form-label<br>
                    .lsb-form-input<br>
                    .lsb-form-input<br>
                    .lsb-form-checks<br>
                    .lsb-form-checks<br>
                    .lsb-form-check<br>
                    .lsb-form-check-input<br>
                    .lsb-form-check-label<br>
                    .lsb-form-button<br>
                    .lsb-form-global-error<br>
                    .lsb-success<br>
                    .lsb-success-title<br>
                    .lsb-success-text
                </code>
                <p>
                    De &lt;form&gt; tag krijgt nog aanvullende classes mee:<br>
                    <code class="laposta-code">&lt;form class="lsb-form lsb-list-id-[listId]"&gt;</code>
                    Waarbij [listId] vervangen wordt voor het id van de lijst.
                </p>
                <p>Verder krijgen de field wrappers als extra class de relatievariabele (tag) van dat specifieke veld mee, alsmede de het veld type:
                    <code class="laposta-code">&lt;div class="lsb-field-tag-[tag] lsb-field-type-[fieldType] [misc-classes]"&gt;</code>
                    Waarbij [tag] vervangen wordt voor de relatievariebele (tag) van de lijst, <br>
                    [fieldType] wordt vervangen voor het type veld (text, email, number, date, select, radio, checkbox)<br>
                    en [misc-classes] wordt vervangen voor class die hoort bij de gekozen styles + onze eigen class 'lsb-form-field-wrapper'.
                    <br>
                    Door deze combinatie van unieke formulier- en veldeigenschappen kunt u ieder veld apart beïnvloeden met behulp van CSS.
                </p>

            </section>

            <section class="lsb-settings__inline-css">
                <h2>Inline CSS</h2>
                <p>In dit veld kunt u eigen CSS invoeren. De CSS zal uitsluitend inline worden toegevoegd op pagina's waar de shortcode wordt toegevoegd.</p>
                <label class="lsb-settings__inline-css-label" for="<?= Plugin::OPTION_INLINE_CSS ?>">Inline css invoerveld</label>
                <textarea
                    class="lsb-settings__inline-css-input"
                    name="<?= Plugin::OPTION_INLINE_CSS ?>"
                    id="<?= Plugin::OPTION_INLINE_CSS ?>"
                    placeholder=".lsb-list-id-12345 .eigen-label-class {
    font-weight: bold;
}

.gekozen-field-wrapper-class.field-type-email .gekozen-label-class {
    font-color: red;
}
"
                ><?= get_option(Plugin::OPTION_INLINE_CSS, '') ?></textarea>
            </section>

            <section class="lsb-settings__misc">
                <h2>Overige instellingen</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="<?= Plugin::OPTION_CLASS_GLOBAL_ERROR ?>">Global error class</label></th>
                        <td><input
                                type="text"
                                name="<?= Plugin::OPTION_CLASS_GLOBAL_ERROR ?>"
                                id="<?= Plugin::OPTION_CLASS_GLOBAL_ERROR ?>"
                                value="<?= get_option(Plugin::OPTION_CLASS_GLOBAL_ERROR) ?>"
                                placeholder="global-error-class"
                            >
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="<?= Plugin::OPTION_CLASS_SUCCESS_WRAPPER ?>">Successfully subscribed wrapper class</label></th>
                        <td><input
                                    type="text"
                                    name="<?= Plugin::OPTION_CLASS_SUCCESS_WRAPPER ?>"
                                    id="<?= Plugin::OPTION_CLASS_SUCCESS_WRAPPER ?>"
                                    value="<?= get_option(Plugin::OPTION_CLASS_SUCCESS_WRAPPER) ?>"
                                    placeholder="success-wrapper-class"
                            >
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="<?= Plugin::OPTION_CLASS_SUCCESS_TITLE ?>">Successfully subscribed title class</label></th>
                        <td><input
                                    type="text"
                                    name="<?= Plugin::OPTION_CLASS_SUCCESS_TITLE ?>"
                                    id="<?= Plugin::OPTION_CLASS_SUCCESS_TITLE ?>"
                                    value="<?= get_option(Plugin::OPTION_CLASS_SUCCESS_TITLE) ?>"
                                    placeholder="success-title-class"
                            >
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="<?= Plugin::OPTION_CLASS_SUCCESS_TEXT ?>">Successfully subscribed text class</label></th>
                        <td><input
                                    type="text"
                                    name="<?= Plugin::OPTION_CLASS_SUCCESS_TEXT ?>"
                                    id="<?= Plugin::OPTION_CLASS_SUCCESS_TEXT ?>"
                                    value="<?= get_option(Plugin::OPTION_CLASS_SUCCESS_TEXT) ?>"
                                    placeholder="success-text-class"
                            >
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="<?= Plugin::OPTION_SUCCESS_TITLE ?>">Successfully subscribed title</label></th>
                        <td><input
                                    type="text"
                                    name="<?= Plugin::OPTION_SUCCESS_TITLE ?>"
                                    id="<?= Plugin::OPTION_SUCCESS_TITLE ?>"
                                    value="<?= get_option(Plugin::OPTION_SUCCESS_TITLE) ?>"
                                    placeholder="Succesvol aangemeld"
                            >
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="<?= Plugin::OPTION_SUCCESS_TEXT ?>">Successfully subscribed text</label></th>
                        <td><textarea
                                    name="<?= Plugin::OPTION_SUCCESS_TEXT ?>"
                                    id="<?= Plugin::OPTION_SUCCESS_TEXT ?>"
                                    placeholder="Het aanmelden is gelukt."
                                    rows="=3"
                            ><?= get_option(Plugin::OPTION_SUCCESS_TEXT) ?></textarea>
                        </td>
                    </tr>

                </table>
            </section>
        </div>

        <?php @submit_button(); ?>

    </form>





</div>