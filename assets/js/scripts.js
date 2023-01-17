(function ($) {

    /*** Sticky header */
    $(window).scroll(function () {
        if ($("body").scrollTop() > 0 || $("html").scrollTop() > 0) {
            $(".dashboard__header").addClass("sticky-header");
        } else {
            $(".dashboard__header").removeClass("sticky-header");
        }
    });

    $(document).on('click', '.dashboard__header .navbar-toggle', function (e) {
        $(this).toggleClass('in');
        $('.hoodslyhub-user-dashboard').toggleClass('hoodslyhub-navbar-toggle');
    });

    var myElement = document.getElementById('simplebar');
    new SimpleBar(myElement, {autoHide: true});

    var historySimplebar = document.getElementById('history-simplebar');
    var notification_scroll = document.getElementById('order_notification_list');
    var CliamSimplebar    = document.querySelectorAll('.cliam-simplebar');

    if (historySimplebar) {
        new SimpleBar(historySimplebar, {autoHide: true});
    }

    if (notification_scroll) {
        new SimpleBar(notification_scroll, {autoHide: true});
    }

    if (CliamSimplebar) {
        $('.cliam-simplebar').each(function(){
            new SimpleBar($(this)[0], {autoHide: true});
        });
    }

    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip({
            html: true,
        });

        $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
            $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
        });
    });

    /*** Header height = gutter height */
    function setGutterHeight() {
        var header = document.querySelector('.dashboard__header'),
            gutter = document.querySelector('.header-gutter');
        if (gutter) {
            gutter.style.height = header.offsetHeight + 'px';
        }
    }

    window.onload   = setGutterHeight;
    window.onresize = setGutterHeight;

    $(document).on('click', '.wilkeshub-delete-order', function (e) {
        e.preventDefault();
        //console.log($(this))
        let order_id = $(this).data('orderid');
        let nonce    = $(this).data('nonce');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            showLoaderOnConfirm: true,
            preConfirm: function () {
                return new Promise(function (resolve) {
                    $.ajax({
                        type: 'post',
                        url: ajaxRequest.ajaxurl,
                        data: {
                            action: 'wilkeshub_delete_order',
                            nonce: nonce,
                            id: order_id
                        },
                        success: function (data) {
                            if (data) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Your Order has been deleted..',
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong.',
                            })
                        }
                    })
                });
            },
            allowOutsideClick: false
        });
    })

    $('.submit_vent_tracking').on('click', function (e) {
		e.preventDefault();
		var vent_shipping_method = $('.vent_shipping_method').val();
		var vent_tracking_num = $('.vent_tracking_num').val();
		var post_id = $('.post_id').val();
		var vent_email = $('.vent_email').val();
        console.log(vent_email);
		if(vent_tracking_num !=="" && vent_shipping_method !== "Select.."){
			$('.alert-danger').css("display", "none")
			//Ajax Function to send a get request
			$.ajax({
				type: "POST",
				url: hub_obj.ajaxurl,
				data: {
					action: "add_ventilation_tracking",
					vent_shipping_method: vent_shipping_method,
					vent_tracking_num: vent_tracking_num,
					post_id: post_id,
					vent_email: vent_email,
				},
                beforeSend: function() {
                    Swal.fire({
                        title: 'Sending...',
                        showConfirmButton: false,
                        allowEscapeKey: false,
                        allowOutsideClick: false,

                    });
                    Swal.showLoading();
                },
				success: function(response){
					//if request if made successfully then the response represent the data
					$( "#result" ).empty().append( response );
                    if (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sent!',
                            text: 'Email has been sent successfully.',
                            showConfirmButton: false,
                            timer: 2000
                        })
                    }
				},
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Emails not sending. Something went wrong.',
                        showConfirmButton: false,
                        timer: 2000
                    })
                }
			});
		}else{
			$('.alert-danger').css("display", "block")
		}
	})

    /*** Select Field Custom */
    $('.order-select').each(function(){
        var $this = $(this), numberOfOptions = $(this).children('option').length;
        var overflow = numberOfOptions > 5 ? 'overflow-y' : '';
        $this.addClass('select-hidden'); 
        $this.wrap('<div class="select"></div>');
        $this.after('<div class="select-styled"></div>');

        var $styledSelect = $this.next('div.select-styled');
        $styledSelect.text($this.children('option').eq(0).text());
      
        var $list = $('<ul />', {
            'class': 'select-options'
        }).insertAfter($styledSelect);
      
        for (var i = 0; i < numberOfOptions; i++) {
            $('<li />', {
                text: $this.children('option').eq(i).text(),
                rel: $this.children('option').eq(i).val()
            }).appendTo($list);
        }
      
        var $listItems = $list.children('li');
      
        $styledSelect.click(function(e) {
            e.stopPropagation();
            $('div.select-styled.active').not(this).each(function(){
                $(this).removeClass('active').next('ul.select-options').hide();
            });
            $(this).toggleClass('active').next('ul.select-options').addClass(overflow).toggle();
        });
      
        $listItems.click(function(e) {
            e.stopPropagation();
            $styledSelect.text($(this).text()).removeClass('active');
            $this.val($(this).attr('rel'));
            $('select option').removeAttr('selected');
            $('select option[value="'+$(this).attr('rel')+'"]').attr('selected','selected');
            // Only Woo Orderby
            if ($this.hasClass('orderby')) {
                $(this).closest( 'form' ).submit();
            }
            $list.hide();
        });
      
        $(document).click(function() {
            $styledSelect.removeClass('active');
            $list.hide();
        });
    });

    /*
      * Ventilation request
      * */
    $(document).on('click', '.request_vent', function (e) {
        e.preventDefault();
        let post_id = $(this).data('postid');
        let nonce   = $(this).data('nonce');
        let order_id   = $(this).data('orderid');
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'request_ventilation',
                nonce: nonce,
                postid: post_id,
                orderid: order_id,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Requesting...',
                    text: 'Requesting Ventilation.',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Requested!',
                        text: 'Requested ventilations successfully.',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Requested proses failed. Something went wrong.',
                        showConfirmButton: false,
                        timer: 3000
                    })
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'Requested proses failed. Something went wrong.',
                    showConfirmButton: false,
                    timer: 3000
                })
            }
        })
    })

    /**
     * Shop Custom Color match Action - Received
     */
    $(document).on('click', '.wilkes_ccm_received', function (e) {
        e.preventDefault();
        let post_id = $(this).data('postid');
        let nonce = $(this).data('nonce');
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'wilkes_ccm_received_action',
                nonce: nonce,
                post_id: post_id,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Receiving...',
                    text: 'Receiving Custom Color Match Order.',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Received!',
                        text: 'Received custom color match samples.',
                        showConfirmButton: true,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        //timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Receiving proses failed. Something went wrong.',
                        showConfirmButton: true,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'Receiving proses failed. Something went wrong.',
                    showConfirmButton: true,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    //timer: 3000
                }).then(() => {
                    location.reload();
                });
            }
        })
    })

    /**
     * Shop Custom Color match Action - Send To Be Matched
     */
    $(document).on('click', '.ccm_send_to_be_matched', function (e) {
        e.preventDefault();
        let post_id = $(this).data('postid');
        let nonce = $(this).data('nonce');
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'wilkes_ccm_send_to_be_matched_action',
                nonce: nonce,
                post_id: post_id,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Sending...',
                    text: 'Sending Custom Color Match Samples for Matched.',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Received!',
                        text: data.msg,
                        showConfirmButton: true,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        //timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Sent to be matched proses failed. Something went wrong.',
                        showConfirmButton: true,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        //timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'Proses failed. Something went wrong.',
                    showConfirmButton: true,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    //timer: 3000
                }).then(() => {
                    location.reload();
                });
            }
        })
    })

    /**
     * Shop Custom Color match Action - Matched
     */
    $(document).on('click', '.ccm_matched', function (e) {
        e.preventDefault();
        let post_id = $(this).data('postid');
        let nonce = $(this).data('nonce');
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'wilkes_ccm_matched_action',
                nonce: nonce,
                post_id: post_id,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Matching on the process.',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Matched!',
                        text: data.msg,
                        showConfirmButton: true,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        //timer: 3000
                    }).then(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Order In Production',
                            showConfirmButton: true,
                            allowEscapeKey: false,
                            allowOutsideClick: false,
                            //timer: 3000
                        }).then(() => {
                            location.reload();
                        });
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Matched proses failed. Something went wrong.',
                        showConfirmButton: true,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        //timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'Proses failed. Something went wrong.',
                    showConfirmButton: true,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    //timer: 3000
                }).then(() => {
                    location.reload();
                });
            }
        })
    })

    /*
    * Order pending to Productuion
    * */
    $(document).on('click', '.wilkes-pending-status-action', function (e) {
        e.preventDefault();
        let post_id = $(this).data('postid');
        let order_id = $(this).data('orderid');
        let nonce = $(this).data('nonce');
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'wilkes_order_pending_to_production',
                nonce: nonce,
                post_id: post_id,
                order_id: order_id,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Order Processing for Production.',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Received!',
                        text: data.msg,
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Order proses failed. Something went wrong.',
                        showConfirmButton: false,
                        timer: 3000
                    })
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'Order proses failed. Something went wrong.',
                    showConfirmButton: false,
                    timer: 3000
                })
            }
        })
    })
    $('.select_status').on('change', function () {
		var orderid_array = [];
		var postid_array = [];
        $('.bulk_check:checked').each(function(i){
			orderid_array[i] = $(this).data("orderid");
			postid_array[i] = $(this).data("postid");
        });
		console.log($(this).val())
		if(orderid_array == ''){
			return false;
		}
		$.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'update_order_status_bulk',
                orderid_array: orderid_array,
                postid_array: postid_array,
                status_label: $(this).val()
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Please Wait...',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated Status',
                        text: 'Order Updated Status Successfully.',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Bulk Updating Status Failed...Please try again',
                        showConfirmButton: false,
                        timer: 3000
                    })
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'failed. Something went wrong.',
                    showConfirmButton: false,
                    timer: 3000
                })
            }
        })
	});

	$('.bulk_edit').on('click', function () {
		var orderurl_array = [];
        $('.bulk_check:checked').each(function(i){
			orderurl_array[i] = $(this).data("orderurl");
        });
		console.log(orderurl_array)
		if(orderurl_array == ''){
			return false;
		}
		orderurl_array.forEach(function(item) {
			window.open(item);
		});
	})

    /**
     * wilkes Order Pagination
     */
    $('#wilkes-order-list').on('click', '#wilkesPaginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="full_p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#wilkesPaginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "wilkes_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#wilkes-order-list .table-order tbody').html(ajaxDiv);
            },
            success: function (data) {
                $('#wilkes-order-list .table-order tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#wilkesPaginate').html(data);
            }
        });
    });
    //End wilkes Order Pagination

    /**
     * wilkes pending Order Pagination
     */
    $('#wilkes-pending-order-list').on('click', '#wilkespendingPaginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="half_p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#wilkespendingPaginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pending_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#wilkes-pending-order-list .has--custom-color tbody').html(ajaxDiv);
            },
            success: function (data) {
                console.log(data)
                $('#wilkes-pending-order-list .has--custom-color tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#wilkespendingPaginate').html(data);
            }
        });
    });
    //End pending Order Pagination

    /**
     * wilkes Completed Order Pagination
     */
    $('#completed-order-list').on('click', '#completedPaginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="full_p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#completedPaginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "completed_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#completed-order-list .table-order tbody').html(ajaxDiv);
            },
            success: function (data) {
                $('#completed-order-list .table-order tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#completedPaginate').html(data);
            }
        });
    });
    //End Completed Order Pagination

    /**
     * wilkes CCM Order Pagination
     */
    $('#ccm-order-list').on('click', '#ccmPaginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="half_p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#ccmPaginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "ccm_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#ccm-order-list .has--custom-color tbody').html(ajaxDiv);
            },
            success: function (data) {
                $('#ccm-order-list .has--custom-color tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#ccmPaginate').html(data);
            }
        });
    });
    //End CCm Order Pagination

    /**
     * wilkes Vent Order Pagination
     */
    $('#vent-order-list').on('click', '#ventPaginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="full_p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#ventPaginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "vent_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#vent-order-list .table-order tbody').html(ajaxDiv);
            },
            success: function (data) {
                $('#vent-order-list .table-order tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#ventPaginate').html(data);
            }
        });
    });
    //End Vent Order Pagination

    /**
     * wilkes Vent completed Order Pagination
     */
    $('#vent-completed-list').on('click', '#ventcompletedPaginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="full_p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#ventcompletedPaginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "vent_completed_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#vent-completed-list .table-order tbody').html(ajaxDiv);
            },
            success: function (data) {
                $('#vent-completed-list .table-order tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#ventcompletedPaginate').html(data);
            }
        });
    });
    //End Vent completed Order Pagination

    /**
     * Floating Shelves Order Pagination
     */
    $('#floating-shelves-order-list').on('click', '#floatingShelvesPaginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="full_p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#floatingShelvesPaginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "floating_shelves_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#floating-shelves-order-list .table-order tbody').html(ajaxDiv);
            },
            success: function (data) {
                $('#floating-shelves-order-list .table-order tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#floatingShelvesPaginate').html(data);
            }
        });
    });
    //End Floating Shelves Order Pagination

    /**
     * wilkes Completed floating shelves Order Pagination
     */
    $('#completed-floating-order-list').on('click', '#completedfloatingPaginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="full_p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#completedfloatingPaginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "completed_floating_shelves_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#completed-floating-order-list .table-order tbody').html(ajaxDiv);
            },
            success: function (data) {
                $('#completed-floating-order-list .table-order tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#completedfloatingPaginate').html(data);
            }
        });
    });

    /**
     * Damage image magnific popup
     */
    $('.gallery-popup-item').magnificPopup({
        type: 'image',
        // midClick: true,
        // fixedBgPos: true,
        // removalDelay: 500,
        // fixedContentPos: true,
        // tLoading: 'Loading image #%curr%...',
        // gallery: {
        //     enabled: true,
        //     preload: [0, 1],
        //     navigateByImgClick: true,
        // },
        // image: {
        //     tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
        //     titleSrc: function (item) {
        //         return item.el.find('img').attr('alt');
        //     },
        // },
        // callbacks: {
        //     beforeOpen: function () {
        //         this.st.image.markup = this.st.image.markup.replace(
        //             'mfp-figure',
        //             'mfp-figure mfp-with-anim'
        //         );
        //         this.st.mainClass    =
        //             'mfp-move-from-top vertical-middle mfp-popup-gallery';
        //     },
        //     buildControls: function () {
        //         // re-appends controls inside the main container
        //         this.arrowLeft.appendTo(this.contentContainer);
        //         this.arrowRight.appendTo(this.contentContainer);
        //         this.currTemplate.closeBtn.appendTo(this.contentContainer);
        //     },
        // },
    });

    /**
     * Shop claim Action
     */
    $(document).on('click', '.shop_claim_approved', function (e) {
        e.preventDefault();
        let post_id = $(this).data('postid');
        let nonce    = $(this).data('nonce');
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'shop_claim_approved_request',
                nonce: nonce,
                post_id: post_id,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Approving...',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Done!',
                        text: 'Approved shop claim',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: '. Something went wrong.',
                        showConfirmButton: false,
                        timer: 3000
                    })
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'Approving failed. Something went wrong.',
                    showConfirmButton: false,
                    timer: 3000
                })
            }
        })
    })

    /**
     * Bulk Edit, Status change and BOL download functionality
     */
    $('.select_status').on('change', function () {
		var orderid_array = [];
		var postid_array = [];
        $('.bulk_check:checked').each(function(i){
			orderid_array[i] = $(this).data("orderid");
			postid_array[i] = $(this).data("postid").trim();
        });
		console.log($(this).val())
		if(orderid_array == ''){
			return false;
		}
		$.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'update_order_status_bulk',
                orderid_array: orderid_array,
                postid_array: postid_array,
                status_label: $(this).val()
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Please Wait...',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated Status',
                        text: 'Order Updated Status Successfully.',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Bulk Updating Status Failed...Please try again',
                        showConfirmButton: false,
                        timer: 3000
                    })
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'failed. Something went wrong.',
                    showConfirmButton: false,
                    timer: 3000
                })
            }
        })
	});

	$('.bulk_edit').on('click', function () {
		var orderurl_array = [];
        $('.bulk_check:checked').each(function(i){
			orderurl_array[i] = $(this).data("orderurl");
        });
		console.log(orderurl_array)
		if(orderurl_array == ''){
			return false;
		}
		orderurl_array.forEach(function(item) {
			window.open(item);
		});
	})
	$('.bulk_download_bol').on('click', function () {
		var orderid_array = [];
		var bol_id_array = [];
		var postid_array = [];
        $('.bulk_check:checked').each(function(i){
			orderid_array[i] = $(this).data("orderid");
			bol_id_array[i] = $(this).data("bol_id");
            postid_array[i] = $(this).data("postid");
        });
		$.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'bulk_download_bol',
                orderid_array: orderid_array,
                postid_array: postid_array,
                bol_id_array: bol_id_array
            },
            success: function (data) {
                console.log(data)
                window.open(data.bulk_link);
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'failed. Something went wrong.',
                    showConfirmButton: false,
                    timer: 3000
                })
            }
        })
		
		//window.open('http://localhost/wrh_hub/wp-content/uploads/bol/Bulk-Bol_List.pdf');
	})
    /**
     * Added print order details functionality
     */
     $(document).on('click', '.print_order', function (e) {
        var orderid_array = [];
		var bol_id_array = [];
		var postid_array = [];
        $('.bulk_check:checked').each(function(i){
			orderid_array[i] = $(this).data("orderid");
			bol_id_array[i] = $(this).data("bol_id");
            postid_array[i] = $(this).data("postid");
        });

        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'print_order_details',
                nonce: nonce,
                orderid: orderid,
            },
            success: function (data) {
                popupWin.document.open();
                popupWin.document.write(
                    '<html><body onload="window.print()">' +
                    data +
                    "</html>"
                );
                popupWin.document.close();
            },
            error: function () {

            }
        })
    })

    /**
     * Added print order details functionality
     */
    $(document).on('click', '.bulk_print_work_order', function (e) {
        let orderid          = $(this).data('orderdata');
        var popupWin         = window.open("", "_blank", "width=800,height=1000");
        var print_order_data = $(this).closest('.print_order_data');
        var divToPrint       = document.querySelector('.order_' + orderid);
        let nonce            = $(this).data('nonce');

        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'print_work_order',
                nonce: nonce,
                orderid: orderid,
            },
            success: function (data) {
                popupWin.document.open();
                popupWin.document.write(
                    '<html><body onload="window.print()">' +
                    data +
                    "</html>"
                );
                popupWin.document.close();
            },
            error: function () {

            }
        })
    })
}(jQuery));
