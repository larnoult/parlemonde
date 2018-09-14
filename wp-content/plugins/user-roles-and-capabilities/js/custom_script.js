(function ($) {



    // Validation for Role ID
    $.validator.addMethod("role_id", function (value, element) {
        return this.optional(element) || /^[a-zA-Z0-9-_]+$/.test(value);
    }, "");


    $.validator.addMethod("select_one", function (value, element) {
        var count = $(element).find('option:selected').length;
        return count > 0;
    }, "Please select at least one role!");

    // role already 
    $.validator.addMethod("role_exist", function (value, element) {
        var solvease_wp_roles_obj = jQuery.parseJSON(solvease_wp_roles.solvease_wp_roles);
        if (value.trim() == 'administrator' || typeof solvease_wp_roles_obj[value.trim()] !== 'undefined') {
            return false;
        }
        return true;
    }, "This role ID already exist.");

    $.validator.addMethod("role_name_exist", function (value, element) {
        if (value.trim() === 'Administrator') {
            return false;
        }
        var status = true;
        $.each(jQuery.parseJSON(solvease_wp_roles.solvease_wp_roles), function (index, existing_value) {
            if (existing_value.name === value.trim()) {
                status = false;
            }
        });
        return status;
    }, "This Display name already exist.");


    $.validator.addMethod("cap_exist", function (value, element) {
        var status = true;
        $.each(jQuery.parseJSON(solvease_wp_roles.solvease_wp_roles), function (index, existing_value) {
            if (typeof existing_value.capabilities[value] !== 'undefined') {
                status = false;
            }
        });
        return status;
    }, "This Capability already exist.");


    // final version is here
    $(document).ready(function () {


        $("#cap_role_export").val('');

        $('body').on('click', function (e) {
            $('[data-toggle="popover"]').each(function () {
                //the 'is' for buttons that trigger popups
                //the 'has' for icons within a button that triggers a popup
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                    $(this).popover('hide');
                }
            });
        });


        if ($("table.solvease-rnc-table-head").length > 0) {
            $('[data-toggle="tooltip"]').tooltip();
            $("table.solvease-rnc-table-head").stickyTableHeaders();
        }
        var $uniformed;
        $uniformed = $("#solvease_capability_form tbody").find("input").not(".skipThese");
        if ($uniformed.length) {
            $uniformed.uniform();
        }

        // On enter key made it default
        $('input#filter-capability').keydown(function (event) {
            var keypressed = event.keyCode || event.which;
            if (keypressed == 13) {
                return false;
            }

        });
        /* Filter Capability Function */
        $('input#filter-capability').keyup(function (event) {
            var keypressed = event.keyCode || event.which;

            // do nothing on enter press
            if (keypressed == 13) {
                return false;
            }
            // length should be greater than 0
            if ($('input#filter-capability').val().trim().length < 2) {
                $(".cap-name-to-filter").removeClass('green');
                $('input#filter-capability').closest('table').find('tr').show();
                return;
            }
            $('input#filter-capability').closest('table').find('tr').show();

            // add class green when there is match
            var regxp = new RegExp($('input#filter-capability').val().trim());
            $(".cap-name-to-filter").each(function () {

                if (regxp.test($(this).text())) {
                    $(this).addClass('green');
                    //console.log($(this).closest('tr').prev());
                    //console.log($(this).closest('tr').next());
                } else {
                    $(this).closest('tr').hide();
                    $(this).removeClass('green');
                }
            });


            $("tr.solvease-rnc-head-start").each(function () {
                var headRowID = $(this).attr('head-row-id');
                if($(".cap-name-to-filter."+headRowID).is(":visible") !== true){
                    $(this).hide();
                }
            })
        });


        $('#solvease_add_role_form').validate({
            rules: {
                'role-id': {
                    minlength: 3,
                    maxlength: 15,
                    required: true,
                    role_id: true,
                    role_exist: true
                },
                'role-name': {
                    minlength: 3,
                    maxlength: 15,
                    required: true,
                    role_name_exist: true
                }
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            }
        });


        $('#solvease_add_capability_form').validate({
            rules: {
                'cap_name': {
                    minlength: 3,
                    required: true,
                    cap_exist: true
                },
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            }
        });


        $("#solvease_export_capability").validate({
            rules: {
                cap_role_export: {
                    select_one: true
                }
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            submitHandler: function (form) {
                $(".loading-icon").show();
                var data = {
                    'action': 'export_role_cap',
                    'roles_to_export': $("#cap_role_export").val()
                };

                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: data,
                    dataType: "json",
                    success: function (response, status, xhr) {
                        $(".loading-icon").hide();
                        $("#cap_role_export").val('');
                        $('button.export-close').trigger('click');
                        // check for a filename
                        response = JSON.stringify(response);
                        var filename = "";
                        var disposition = xhr.getResponseHeader('Content-Disposition');
                        if (disposition && disposition.indexOf('attachment') !== -1) {
                            var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                            var matches = filenameRegex.exec(disposition);
                            if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                        }

                        var type = xhr.getResponseHeader('Content-Type');
                        console.log(type);
                        var blob = new Blob([response], {type: type});

                        if (typeof window.navigator.msSaveBlob !== 'undefined') {
                            // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
                            window.navigator.msSaveBlob(blob, filename);
                        } else {
                            var URL = window.URL || window.webkitURL;
                            var downloadUrl = URL.createObjectURL(blob);

                            if (filename) {
                                // use HTML5 a[download] attribute to specify filename
                                var a = document.createElement("a");
                                // safari doesn't support this yet
                                if (typeof a.download === 'undefined') {
                                    window.location = downloadUrl;
                                } else {
                                    a.href = downloadUrl;
                                    a.download = filename;
                                    document.body.appendChild(a);
                                    a.click();
                                }
                            } else {
                                window.location = downloadUrl;
                            }

                            setTimeout(function () {
                                URL.revokeObjectURL(downloadUrl);
                            }, 100); // cleanup
                        }
                    }
                });
                return false;
            }
        });

        $(".role-opertaion .select-all").click(function () {
            $("input[name^='capability[" + $(this).parents('div.role-opertaion').attr('role-id') + "']").each(function () {
                if ($(this).prop('checked') === false) {
                    $(this).trigger('click');
                }
            });
        })

        $(".role-opertaion .un-select-all").click(function () {
            $("input[name^='capability[" + $(this).parents('div.role-opertaion').attr('role-id') + "']").each(function () {
                if ($(this).prop('checked') === true) {
                    $(this).trigger('click');
                }
            });
        })


    });
})(jQuery);


