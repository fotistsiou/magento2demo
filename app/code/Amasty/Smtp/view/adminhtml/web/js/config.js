define([
    'jquery',
    'Magento_Ui/js/modal/alert'
], function ($, alert) {

    $.widget('mage.amsmtpConfig', {
        options: {
            selectors: {},
            providers: [],
            childFrame: null,
            successClass: 'success',
            failClass: 'fail',
            loadingClass: 'loading',
            configFormId: 'config-edit-form',
            testAndSaveButtonId: '#amsmtp_smtp_test_email',
            microsoftOptions: {
                window: {
                    title: $.mage.__('Microsoft Auth'),
                    params: 'width=700,height=620,popup=true',
                },
                authMethodName: 'xoauth2_microsoft_client'
            },
            alertPopup: {
                modalWindow: 'amsmtp-modal-window',
                contentSelector: '.amsmtp-modal .amsmtp-modal-body-content',
                errorClassName: 'message-error',
                successClassName: 'message-success',
                messageBlock: '.message',
                defaultErrorMessage: $.mage.__('Something went wrong')
            },
        },

        elements: {},

        detailsElement: null,

        _create: function () {
            for (var field in this.options.selectors) {
                this.elements[field] = $(this.options.selectors[field]);
            }

            this.detailsElement = this.element.find('[data-role=details]');

            this.detailsElement.find('[data-role=toggle]').click(function () {
                $(this).parent('[data-role=details]').toggleClass('collapsed');
            });

            var fillTrigger = $(this.element).find('[data-role="amsmtp-fill-button"]');

            if (fillTrigger) {
                fillTrigger.click($.proxy(this.fill, this))
            }

            var checkTrigger = $(this.element).find('[data-role="amsmtp-check-button"]');

            if (checkTrigger) {
                checkTrigger.click($.proxy(this.active, this));
            }

            this.toggleNoticeVisibility();
            $('.amsmtp-enable').on('click', this.toggleNoticeVisibility);
        },

        getPopupMessageBlock: function () {
            return $(this.options.alertPopup.contentSelector + ' ' + this.options.alertPopup.messageBlock);
        },

        active: function (event) {
            event.preventDefault();

            $(this.options.testAndSaveButtonId).toggleClass('required-entry');
            this.setAction();
            $(this.options.testAndSaveButtonId).toggleClass('required-entry');
        },

        setAction: function() {
            if ($(this.options.selectors.auth).val() === this.options.microsoftOptions.authMethodName
                && this.options.microsoft_auth_url !== undefined
            ) {
                this.openAuthPopup();
            } else {
                $('#' + this.options.configFormId).attr('action', this.options.check_url).submit();
            }
        },

        openAuthPopup: function() {
            var self = this;

            this.options.childFrame = window.open(
                self.options.microsoft_auth_url,
                self.options.microsoftOptions.window.title,
                self.options.microsoftOptions.window.params
            );

            this.checkFrameStatus();
        },

        checkFrameStatus: function () {
            var self = this,
                timer;

            timer = setInterval(function () {
                if (self.options.childFrame.closed) {
                    if (document.isTokenGenerated) {
                        self.options.microsoft_auth_url = undefined;
                    }
                    self.showPopup();

                    clearInterval(timer);
                }
            }, 1000);
        },

        showPopup: function () {
            var self = this;
            this.generatePopupContent();
            alert({
                title: $.mage.__('Test connection results'),
                content: $(this.options.alertPopup.contentSelector).html(),
                modalClass: this.options.alertPopup.modalWindow,
                buttons: [{
                    text: $.mage.__('OK'),
                    class: 'action-primary action-accept',
                    click: function () {
                        self.getPopupMessageBlock().hide();
                        self.getPopupMessageBlock().removeClass([
                            self.options.alertPopup.errorClassName,
                            self.options.alertPopup.successClassName
                        ]);
                        this.closeModal(true);
                    }
                }]
            });
        },

        generatePopupContent: function () {
            var isSuccess = document.isTokenGenerated;
            var messageBlock = this.getPopupMessageBlock();
            var selector;

            if (isSuccess) {
                selector = messageBlock.addClass(this.options.alertPopup.successClassName);
                selector.text($.mage.__('Connection Successful!'));
            } else {
                selector = messageBlock.addClass(this.options.alertPopup.errorClassName);
                selector.text(this.prepareErrorText());
            }
            messageBlock.show();
        },

        prepareErrorText: function() {
            return document.errorText && document.errorText.length > 0
                ? document.errorText
                : this.options.alertPopup.defaultErrorMessage
        },

        fill: function () {
            var index = +this.elements.provider.val();
            var provider = this.options.providers[index];

            this.elements.server.val(provider.server);
            this.elements.port.val(provider.port);
            this.elements.auth.val(provider.auth);
            if (this.elements.auth.length) {
                this.elements.auth[0].dispatchEvent(new Event('change'));
            }
            this.elements.encryption.val(provider.encryption);
        },

        toggleNoticeVisibility: function () {
            if ($('.amsmtp-enable').val() == '0') {
                $('.amsmtp-notice').show();
            } else {
                $('.amsmtp-notice').hide();
            }
        }
    });

    return $.mage.amsmtpConfig;
});
