
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
 * Description
 *
 * Gamification wheel for offering discount coupons.
 */
//var vid = document.getElementById("kbmyaudio");
//
//function playAudio(vid) {
//    vid.play();
//}
var copy_msg_show = true;
var globaltime = 0;
var intervalVariable;
var wheelStartTime = 0;
var currentRotation = 0;
var rotationDegree = 0;
var wheelEndTime = 0;
var wheelcurrentRotation = 0;
var startTime = 0;
//var winningSlider = 12;
var winningCode = "";
function checkEnteredName(requireName) {
    var is_error = false;
    if ((requireName == 1 && custName == 1) || (requireName == 1 && custName == 3)) {

        var firstname = velovalidation.checkMandatory($("input[name='sw_first_name']"));
        if (firstname !== true) {
            $('#kbsw_first_name').tooltipster({
                animation: 'swing',
                'theme': ['tooltipster-default', 'tooltipster-velsofspinwheel']
            });
            is_error = true;
            $('#kbsw_first_name').tooltipster('content', firstname);
            $('#kbsw_first_name').tooltipster('show');
            setTimeout(function () {
                $('#kbsw_first_name').tooltipster('destroy');

            }, 2000);
            $('#kbsw_first_name').shake({
                interval: 100,
                distance: 20,
                times: 5
            });
        }
    } else if ((requireName == 2 && custName == 2) || (requireName == 2 && custName == 3)) {
        var lastname = velovalidation.checkMandatory($("input[name='sw_last_name']"));
        if (lastname !== true) {
            $('#kbsw_last_name').tooltipster({
                animation: 'swing',
                'theme': ['tooltipster-default', 'tooltipster-velsofspinwheel']
            });
            is_error = true;
            $('#kbsw_last_name').tooltipster('content', lastname);
            $('#kbsw_last_name').tooltipster('show');
            setTimeout(function () {
                $('#kbsw_last_name').tooltipster('destroy');

            }, 2000);
            $('#kbsw_last_name').shake({
                interval: 100,
                distance: 20,
                times: 5
            });
        }
    } else if (requireName == 3 && custName == 3) {
        var firstname = velovalidation.checkMandatory($("input[name='sw_first_name']"));
        if (firstname !== true) {
            $('#kbsw_first_name').tooltipster({
                animation: 'swing',
                'theme': ['tooltipster-default', 'tooltipster-velsofspinwheel']
            });
            is_error = true;
            $('#kbsw_first_name').tooltipster('content', firstname);
            $('#kbsw_first_name').tooltipster('show');
            setTimeout(function () {
                $('#kbsw_first_name').tooltipster('destroy');

            }, 2000);
            $('#kbsw_first_name').shake({
                interval: 100,
                distance: 20,
                times: 5
            });
        }
        var lastname = velovalidation.checkMandatory($("input[name='sw_last_name']"));
        if (lastname !== true) {
            $('#kbsw_last_name').tooltipster({
                animation: 'swing',
                'theme': ['tooltipster-default', 'tooltipster-velsofspinwheel']
            });
            is_error = true;
            $('#kbsw_last_name').tooltipster('content', lastname);
            $('#kbsw_last_name').tooltipster('show');
            setTimeout(function () {
                $('#kbsw_last_name').tooltipster('destroy');

            }, 2000);
            $('#kbsw_last_name').shake({
                interval: 100,
                distance: 20,
                times: 5
            });
        }
    }

    if (is_error) {
        return true;
    } else {
        return false;
    }
}
function checkRequiredGdprService() {
    var error = false;
    $('.vel_gdpr_required').each(function () {
        var currSelectedElement = $('#' + this.id);
        if (typeof $('#' + this.id + ':checked').val() == 'undefined') {
            currSelectedElement.tooltipster({
                animation: 'swing',
                'theme': ['tooltipster-default', 'tooltipster-velsofspinwheel']
            });
            error = true;
            currSelectedElement.tooltipster('content', privacy_error_msg);
            currSelectedElement.tooltipster('show');
            setTimeout(function () {
                currSelectedElement.tooltipster('destroy');

            }, 2000);
            currSelectedElement.shake({
                interval: 100,
                distance: 20,
                times: 5
            });
        }
    });
    return error;
}
function onRotateWheel() {

    var gdpr_error = checkRequiredGdprService();
    var email = $("input[name='spin_wheel_email']").val();
    var email_error = checkEnteredEmail(email);
    var name_error = '';
    if (requireName != 4) {
        name_error = checkEnteredName(requireName);
    } else {
        name_error = false;
    }
    var gdpr_accepted_service = [];
    $("#velsof_offer_main_container input[type='checkbox']:checked").each(function () {
        gdpr_accepted_service.push(this.name);
    });
    if ((email_error === false) && (name_error === false) && (gdpr_error == false)) {
        var firstname = '';
        var lastname = '';

        if (custName == 1) {
            firstname = $('#kbsw_first_name').val();
        } else if (custName == 2) {
            lastname = $('#kbsw_last_name').val();
        } else if (custName == 3) {
            firstname = $('#kbsw_first_name').val();
            lastname = $('#kbsw_last_name').val();
        }
        if (email_recheck == 1) {
            $.ajax({
                url: spin_wheel_front_path,
                type: 'post',
//                data: 'emailRecheck=true&email=' + email,
                data: {"emailRecheck": true, "email": email},
                dataType: 'json',
                success: function (json) {
                    if (json == false) {
                        error = true;
                        $('#velsof_spin_wheel').tooltipster('content', email_check);
                        $('#velsof_spin_wheel').tooltipster('show');
                        setTimeout(function () {
                            $('#velsof_spin_wheel').tooltipster('destroy');
                        }, 2000);
                    } else {
                        var email = $("input[name='spin_wheel_email']").val().trim();
                        $.ajax({
                            url: spin_wheel_front_path,
                            type: 'post',
                            data: {"spinwheelajax": true, "email": email, "firstname": firstname, "lastname": lastname, "wheel_device": wheel_device, "gdpr_service": gdpr_accepted_service},
                            dataType: 'json',
                            beforeSend: function () {
                                $('.saving').show();
                                $('#rotate_btn').hide();
                            },
                            success: function (json) {
                                var code = json['code'];
                                winningCode = json['code'];
                                var slice_no = json['slice_no'];
                                var winningangle = parseInt(720 + ((slice_no - 1) * 30));
                                rotateWheel(winningangle, 9000);

                                setCookie('velsof_wheel_used', 2);
                                $('#suc_msg').html(json['suc_msg']);
                                $('#velsof_success_description').html(json['suc_desc']);

                                if (display_option == '1') {

                                } else {
                                    /*start: changes made by knowband team on 29 Nov 2018 to remove the issue related to oops sorry message.*/
                                    if(display_option == '2'){
                                        if (json['code'] !== '') {
                                            $('#velsof_spin_wheel').val(email_only_msg);
                                            $('#velsof_spin_wheel').show();
                                            //$('#continue_btn').show();
                                        } else{
                                            // $('#velsof_spin_wheel').val('sorry!!');
                                            // $('#velsof_spin_wheel').show();
                                            //   $('#continue_btn').show();
                                        }
                                    }
                                    /*end: changes made by knowband team on 29 Nov 2018 to remove the issue related to oops sorry message.*/
                                    if (json['code'] !== '') {
                                        $.ajax({
                                            url: spin_wheel_front_path,
                                            type: 'post',
                                            data: {"sendEmail": true, "email": email, "firstname": firstname, "lastname": lastname, "code": code, "slice_no": slice_no, "gdpr_service": gdpr_accepted_service},
                                            dataType: 'json',
                                            success: function (json) {
                                            }
                                        });
                                    }
                                }
                            },
                            complete: function () {
                                $('.saving').show();
                                $('#rotate_btn').hide();
                                $('#kbsw_first_name').hide();
                                $('#kbsw_last_name').hide();
                                $('.velsof_spin_wheel_checkbox').remove();
                                window.location = "https://www.aphroditearoma.com/index.php?fc=module&module=kbmobileapp&controller=AppSpinWin&version=1.3&content_only=1&is_wheel_used=1";
                            }
                        });
                    }
                }
            });
        } else {
            var email = $("input[name='spin_wheel_email']").val().trim();
            $.ajax({
                url: spin_wheel_front_path,
                type: 'post',
                data: {"spinwheelajax": true, "email": email, "firstname": firstname, "lastname": lastname, "wheel_device": wheel_device, "gdpr_service": gdpr_accepted_service},
                dataType: 'json',
                beforeSend: function () {
                    $('.saving').show();
                    $('#rotate_btn').hide();

                },
                success: function (json) {
                    var code = json['code'];
                    winningCode = json['code'];
                    var slice_no = json['slice_no'];
                    var winningangle = parseInt(720 + ((slice_no - 1) * 30));
                    rotateWheel(winningangle, 8000);

                    setCookie('velsof_wheel_used', 2);
                    $('#suc_msg').html(json['suc_msg']);
                    $('#velsof_success_description').html(json['suc_desc']);

                    if (display_option == '1') {

                    } else {
                        /*start: changes made by knowband team on 29 Nov 2018 to remove the issue related to oops sorry message.*/
                                    if(display_option == '2'){
                                        if (json['code'] !== '') {
                                            $('#velsof_spin_wheel').val(email_only_msg);
                                            $('#velsof_spin_wheel').show();
                                            //$('#continue_btn').show();
                                        } else{
                                            // $('#velsof_spin_wheel').val('sorry!!');
                                            // $('#velsof_spin_wheel').show();
                                            //   $('#continue_btn').show();
                                        }
                                    }
                        /*end: changes made by knowband team on 29 Nov 2018 to remove the issue related to oops sorry message.*/
                        if (json['code'] !== '') {
                            $.ajax({
                                url: spin_wheel_front_path,
                                type: 'post',
                                data: {"sendEmail": true, "email": email, "firstname": firstname, "lastname": lastname, "code": code, "slice_no": slice_no, "gdpr_service": gdpr_accepted_service},
                                dataType: 'json',
                                success: function (json) {
                                }
                            });
                        }
                    }
                },
                complete: function () {
                    $('.saving').show();
                    $('#kbsw_first_name').hide();
                    $('#kbsw_last_name').hide();
                    $('.velsof_spin_wheel_checkbox').remove();
                    window.location = "https://www.aphroditearoma.com/index.php?fc=module&module=kbmobileapp&controller=AppSpinWin&version=1.3&content_only=1&is_wheel_used=1";
                }
            });
        }
    }
}

function animationFrame(animate)
{
    if (window.requestAnimationFrame) {
        window.requestAnimationFrame(animate);
    } else if (window.webkitRequestAnimationFrame) {
        window.webkitRequestAnimationFrame(animate);
    } else if (window.mozRequestAnimationFrame) {
        window.mozRequestAnimationFrame(animate);
    } else {
        Console.log('Sorry! No Supported Browser');
    }
}

function rotateWheel(degreeToRotate, rotationTime)
{
    if (wheel_sound == '1') {
        setTimeout(function () {
            var audio = new Audio(mediapath + 'views/audio/wheelsound.mp3');
            audio.play();
        }, 100);
    }
    currentRotation = 0;
    rotationDegree = degreeToRotate;
    wheelStartTime = 0;
    wheelEndTime = rotationTime;
    startTime = 0;
    animationFrame(animate);
}

function wheelRotation(movement)
{
    return 1 - Math.pow(1 - movement, 5);
}

function pointerMovement(movement)
{
    var n = (-Math.pow((1 - (movement * 2)), 2) + 1);
    if (n < 0)
        n = 0;
    return n;
}

function animate(timestamp)
{
    if (!startTime) {
        startTime = timestamp;
    }

    wheelStartTime = timestamp - startTime;

    if (wheelStartTime > wheelEndTime) {
        wheelStartTime = wheelEndTime;
    }

    wheelcurrentRotation = wheelRotation(((rotationDegree / wheelEndTime) * wheelStartTime) / rotationDegree);
    currentRotation = wheelcurrentRotation * rotationDegree;

    /** Stop Pointer Movement if wheel rotation is 1 */
    if (wheelcurrentRotation > 0.99) {
        if (wheel_design != "1") {
            $('#velsof_wheel_pointer').css({'transform': 'translateY(0%) rotate3d(0,0,1,0deg)', '-webkit-transform': 'translateY(0%) rotate3d(0,0,1,0deg)'});
        }
    }

    tickerRotation = currentRotation - Math.floor(currentRotation / 360) * 360;
    for (i = 1; i <= 12; i++) {
        if ((tickerRotation >= (i * 30) - 20) && (tickerRotation <= (i * 30)))
        {
            angleRotation = 0.2;
            if (wheelcurrentRotation > angleRotation) {
                angleRotation = wheelcurrentRotation;
            }
            var pointerAngle = pointerMovement(-(((i * 30) - 20) - tickerRotation) / 10) * (30 * angleRotation);
            if (wheel_design != "1") {
                $('#velsof_wheel_pointer').css({'transform': 'translateY(0%)  rotate3d(0,0,1,' + (0 - pointerAngle) + 'deg)', '-webkit-transform': 'translateY(0%)  rotate3d(0,0,1,' + (0 - pointerAngle) + 'deg)'});
            }
        }
    }

    //console.log(wheelcurrentRotation);

    if (wheelcurrentRotation < 1) {
        $('#velsof_spinner').css({'transform': 'rotate3d(0,0,1,' + currentRotation + 'deg)', '-webkit-transform': 'rotate3d(0,0,1,' + currentRotation + 'deg)'});
        animationFrame(animate);
    }
    if (wheelcurrentRotation > .999) {
        $('#main_title').hide();
        $('#velsof_description').hide();
        $('.velsof_ul').hide();
        $('#rotate_btn').hide();
        $('#velsof_spin_wheel').hide();
        $('.saving').hide();
        $('#exit').hide();
        $('#suc_msg').show();
        $('#velsof_success_description').show();

        if (display_option == 2) {
            /*start:changes made by knowband team on 29 Nov 2018 to remover issue of oops sorry message*/
            // $('#velsof_spin_wheel').val(email_only_msg);
            // $('#velsof_spin_wheel').show();
            /*end:changes made by knowband team on 29 Nov 2018 to remover issue of oops sorry message*/
        } else {
            if (winningCode !== '') {
                copy_msg_show = false;
                $('#velsof_spin_wheel').val(winningCode);
                $('#velsof_spin_wheel').show();
                if (show_fireworks == "1") {
                    $('#velsof_wheel_main_container').fireworks();
                }
            }
        }
    }
}

function checkEnteredEmail(email) {
    var error = false;
    $('.spin_error').remove();
    $('#velsof_spin_wheel').tooltipster({
        animation: 'swing',
        'theme': ['tooltipster-default', 'tooltipster-velsofspinwheel']
    });

    var email_mand = velovalidation.checkMandatory($("input[name='spin_wheel_email']"));
    var email_valid = velovalidation.checkEmail($("input[name='spin_wheel_email']"));
    if (email_mand !== true) {
        error = true;
        $('#velsof_spin_wheel').tooltipster('content', email_mand);
        $('#velsof_spin_wheel').tooltipster('show');
        setTimeout(function () {
            $('#velsof_spin_wheel').tooltipster('destroy');

        }, 2000);
        $('#velsof_spin_wheel').shake({
            interval: 100,
            distance: 20,
            times: 5
        });
        return error;
    } else if (email_valid !== true) {
        error = true;
        $('#velsof_spin_wheel').tooltipster('content', email_valid);
        $('#velsof_spin_wheel').tooltipster('show');
        setTimeout(function () {
            $('#velsof_spin_wheel').tooltipster('destroy');
        }, 2000);
        $('#velsof_spin_wheel').shake({
            interval: 100,
            distance: 20,
            times: 5
        });
        return error;
    } else {
        return error;
    }
}

function wheelAction(data, email) {
    $('.before_loader').hide();
    if (data['type'] === 'Win') {
        var code = data['code'];
        var slice_no = data['slice_no'];
        $.ajax({
            url: spin_wheel_front_path,
            type: 'post',
            data: {"sendEmail": true, "email": email, "code": code, "slice_no": slice_no},
            dataType: 'json',
            success: function (json) {
                console.log(json);
            }
        });
        var code = data['code'];
        var label = data['label'];
    }
}

function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0)
            return null;
    } else
    {
        begin += 2;
        var end = document.cookie.indexOf(";", begin);
        if (end == -1) {
            end = dc.length;
        }
    }
    return decodeURI(dc.substring(begin + prefix.length, end));
}
$(document).ready(function () {
    var show = true;

    $('#velsof_wheel_container').show();
    setTimeout(function () {
        $('#velsof_wheel_main_container').addClass('transform');
    }, 500);


    $('.cancel_button').on('click', function () {
        setCookie('velsof_spin_wheel_tab', 3);
        $('#velsof_wheel_main_container').removeClass('transform');
        setTimeout(function () {
            $('#velsof_wheel_container').hide();
            if (show_pull_out == 1) {
                $('#pull_out').show();
            }
        }, 500);
    });

});

function setCookie(cookie_name, cookie_value) {
    date = new Date();
    if (cookie_name == 'velsof_wheel_used') {
        date.setTime(date.getTime() + 24 * 60 * 60 * 1000 * parseInt(Wheel_Display_Interval));
    } else {
        date.setTime(date.getTime() + 24 * 60 * 60 * 1000);
    }
    expires = "; expires=" + date.toUTCString();
    document.cookie = cookie_name + '=' + cookie_value + expires + '; path=/';
}

function copyToClipboard(elem) {
    // create hidden text element, if it doesn't already exist
    var targetId = "_hiddenCopyText_";
    var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
    var origSelectionStart, origSelectionEnd;
    if (isInput) {
        // can just use the original source element for the selection and copy
        target = elem;
        origSelectionStart = elem.selectionStart;
        origSelectionEnd = elem.selectionEnd;
    } else {
        // must use a temporary form element for the selection and copy
        target = document.getElementById(targetId);
        if (!target) {
            var target = document.createElement("textarea");
            target.style.position = "absolute";
            target.style.left = "-9999px";
            target.style.top = "0";
            target.id = targetId;
            document.body.appendChild(target);
        }
        target.textContent = elem.textContent;
    }
    // select the content
    var currentFocus = document.activeElement;
    target.focus();
    target.setSelectionRange(0, target.value.length);

    // copy the selection
    var succeed;
    try {
        succeed = document.execCommand("copy");
    } catch (e) {
        succeed = false;
    }
    // restore original focus
    if (currentFocus && typeof currentFocus.focus === "function") {
        currentFocus.focus();
    }

    if (isInput) {
        // restore prior selection
        elem.setSelectionRange(origSelectionStart, origSelectionEnd);
    } else {
        // clear temporary content
        target.textContent = "";
    }
    return succeed;
}

$.fn.shake = function (settings) {
    if (typeof settings.interval == 'undefined') {
        settings.interval = 100;
    }

    if (typeof settings.distance == 'undefined') {
        settings.distance = 10;
    }

    if (typeof settings.times == 'undefined') {
        settings.times = 4;
    }

    if (typeof settings.complete == 'undefined') {
        settings.complete = function () {
        };
    }

    $(this).css('position', 'relative');

    for (var iter = 0; iter < (settings.times + 1); iter++) {
        $(this).animate({left: ((iter % 2 == 0 ? settings.distance : settings.distance * -1))}, settings.interval);
    }

    $(this).animate({left: 0}, settings.interval, settings.complete);
};