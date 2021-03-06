; // semi-colon is a safety net against concatenated scripts and/or other modules which may not be closed properly.
(function ($) {
    var _defaults = {
        entryId: 'newEntry',
        absenceId: 'newAbsence',
        loader: '<div class="modal-body"><i class="fa fa-spinner fa-pulse"></i></div>'
    };

    /**
     *
     * @type {{init: Function, setDate: Function, newEntry: Function, newAbsence: Function, editEntry: Function, editAbsence: Function, _showModal: Function, _insertForm: Function, _showTabs: Function, _hideTabs: Function, _showEntryTab: Function, _showAbsenceTab: Function}}
     */
    var EntryModal = {
        /**
         * initial method to set up module
         */
        init: function () {
            var self = this;

            if (this.settings.entryUrl == null) {
                jQuery.error('Required setting "entryUrl" can not be empty');
            }
            if (this.settings.absenceUrl == null) {
                jQuery.error('Required setting "absenceUrl" can not be empty');
            }

            $(self.element).find('#modalTabs').find('a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
            });

            $(self.element).find('#modalTabs').on('shown.bs.tab', function (e) {
                var type = $(e.target).data('type');
                if (type == 'entry') {
                    self.newEntry();
                } else if (type == 'absence') {
                    self.newAbsence();
                }
            });
        },

        setDate: function (date) {
            var self = this;
            self.settings.date = date;
        },

        newEntry: function () {
            var self = this;
            self._showTabs();
            self._showModal(self.settings.entryUrl, self.settings.entryId, true);
        },

        newAbsence: function () {
            var self = this;
            self._showTabs();
            self._showModal(self.settings.absenceUrl, self.settings.absenceId, true);
        },

        editEntry: function (id) {
            var self = this;
            self._showEntryTab();
            self._showModal(
                Routing.generate('timeentry_edit', {id: id}),
                self.settings.entryId,
                false
            );
        },

        editAbsence: function (id) {
            var self = this;
            self._showAbsenceTab();
            self._showModal(
                Routing.generate('absence_edit', {id: id}),
                self.settings.entryId,
                false
            );
        },

        _showModal: function (url, divId, useDate) {
            var self = this;
            $(self.element).find('#' + divId).html(self.settings.loader);
            $(self.element).modal('show');
            var data = useDate ? {start: self.settings.date.unix()} : {};
            $.ajax({
                url: url,
                data: data,
                cache: false
            }).done(function (html) {
                self._insertForm(url, divId, html, $(self.element));
                self._initDeleteBtn($(self.element));
            });
        },

        _initDeleteBtn: function(modalElement) {
            var deleteBtn = modalElement.find('.delete');
            deleteBtn.click(function(event) {
                var type = $(event.target).data('type');
                var id = $(event.target).data('id');

                var path = type == 'absence'
                    ? Routing.generate('absence_delete', {id: id})
                    : Routing.generate('timeentry_delete', {id: id});

                deleteBtn.button('loading');
                $.getJSON(
                    path,
                    {},
                    function (response) {
                        if (response.status == 'success') {
                            // reload calendar in background
                            $('#calendar').fullCalendar('refetchEvents');

                            // refresh notifications in background
                            $('.notifications-menu').Notifications('refresh');

                            modalElement.modal('hide');
                        }
                    }
                );
                return false;
            });
        },

        _insertForm: function(url, divId, html, modal) {
            var self = this;
            var container = $(self.element).find('#' + divId);

            // Insert form into container
            container.html(html);

            var form = container.find('form');

            // Insert submit button value as hidden element on click
            form.find('button[type="submit"]').click(function(){
                if($(this).attr('name')) {
                    form.append($("<input type='hidden'>").attr({
                            name: $(this).attr('name'),
                            value: $(this).attr('value')
                        })
                    );
                }
            });

            // Handle submit
            form.submit(function (event) {
                // Set loading animation for all submit buttons
                form.find('button[type="submit"]').button('loading');

                // Submit serialized form with AJAX
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(),
                    success: function (html) {
                        // reload calendar in background
                        $('#calendar').fullCalendar('refetchEvents');

                        // refresh notifications in background
                        $('.notifications-menu').Notifications('refresh');

                        if (html == '') {
                            // Close on success
                            modal.modal('hide');
                        } else {
                            // Reload form on error or 'create and new'
                            self._insertForm(url, divId, html, modal);
                        }
                    }
                });
                return false;
            });
        },

        _showTabs: function () {
            var self = this;
            $(self.element).find('.nav-tabs-custom li').show();
        },

        _hideTabs: function () {
            var self = this;
            $(self.element).find('.nav-tabs-custom li').hide();
        },

        _showEntryTab: function () {
            var self = this;
            self._hideTabs();
            $(self.element).find('.nav-tabs-custom li#entryModalNavTab').show();
        },

        _showAbsenceTab: function () {
            var self = this;
            self._hideTabs();
            $(self.element).find('.nav-tabs-custom li#absenceModalNavTab').show();
        }
    };


    /**
     * [auto-generated code]
     * The actual module constructor
     *
     * @param element
     * @param options
     * @constructor
     */
    function EntryModalConstructor(element, options) {
        this.element = element;
        this.settings = $.extend({}, _defaults, options);
        this.init();
    }

    /**
     * [auto-generated code]
     * Avoid Module.prototype conflicts
     */
    $.extend(EntryModalConstructor.prototype, EntryModal);

    /**
     * [auto-generated code]
     * A module wrapper
     * - preventing against multiple instantiations and
     * - handling method calls and
     * - magic getter & setter
     *
     * @returns {*}
     * @constructor
     */
    $.fn.EntryModal = function () {
        window.uiModuleWrapper = window.uiModuleWrapper || new UIModuleWrapper({});
        return window.uiModuleWrapper.handle("EntryModal", EntryModalConstructor, this, arguments);
    };

})(jQuery, window, document);