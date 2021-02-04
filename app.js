let omgholiday_shopName = Shopify.shop;
let omgholiday_isHome = __st && __st.p == 'home' ? true : false;
let omgholiday_events;

function omgholiday_getJsonFile(events) {
    omgholiday_events = events;
    if(typeof Shopify.designMode === 'undefined'){
        if(typeof window.otCheckExistFile === 'undefined'){
            if(omgholiday_shopName === "shopfayes.myshopify.com"){
                if($('.otHolidayEffects').length >0 && omgholiday_events.length > 0 ){
                    for (var i = 0; i < omgholiday_events.length; i++) {
                        omgholiday_renderEvent(i, omgholiday_events[i])
                    }
                }
        
            }else{
                if (omgholiday_events.length > 0) {
                    $("body").append(`
                        <div class="otHolidayEffects">
                            
                        </div>
                    `);
                    for (var i = 0; i < omgholiday_events.length; i++) {
                        omgholiday_renderEvent(i, omgholiday_events[i])
                    }
                }
            }
        }
        window.otCheckExistFile = false;
    }else{
        console.log("The  Holiday Effect by Omega App doesn't support in the Design Mode");
    }
    
    
    
}

function omgholiday_renderEvent(key, event) {
    $(".otHolidayEffects").append(`
        <div id='holidayEffects-${key}'></div>
    `);
    var numberOfIcons = Number(event.number_of_icons),
        divEffects = $("#holidayEffects-" + key),
        homeCss = omgholiday_renderHomepage(key, event.only_home);

    $("body").append(`
        <style>
            ${homeCss}
            #holidayEffects-${key} > div {width:${event.image_size}px;height:${event.image_size}px;}
            #holidayEffects-${key} > div > img{width:${event.image_size}px;height:${event.image_size}px;}
            #holidayEffects-${key} > div > i{font-size:${event.icon_size}px;color:${event.icon_color};}
        </style>
    `);
    if (event.choose_icons.length == 1) {
        for (var i = 0; i < event.choose_icons.length; i++) {
            for (var j = 0; j < numberOfIcons; j++) {
                divEffects.append(omgholiday_createIconEffects(event.choose_icons[i], Number(event.icon_size), Number(event.animation_speed)));
            }
        }
    } else if (event.choose_icons.length > 1) {
        var number = numberOfIcons;
        for (var i = 0; i < event.choose_icons.length; i++) {
            var random = omgholiday_randomInteger(0, number);
            for (var j = 0; j < random; j++) {
                divEffects.append(omgholiday_createIconEffects(event.choose_icons[i], Number(event.icon_size), Number(event.animation_speed)));
            }
            number = numberOfIcons - number
        }
    }
    if (event.choose_images.length == 1) {
        for (var i = 0; i < event.choose_images.length; i++) {
            for (var j = 0; j < numberOfIcons; j++) {
                divEffects.append(omgholiday_createImageEffects(event.choose_images[i], Number(event.icon_size), Number(event.animation_speed)));
            }
        }
    } else if (event.choose_images.length > 1) {
        var number = numberOfIcons;
        for (var i = 0; i < event.choose_images.length; i++) {
            var random = omgholiday_randomInteger(0, number);
            for (var j = 0; j < random; j++) {
                divEffects.append(omgholiday_createImageEffects(event.choose_images[i], Number(event.icon_size), Number(event.animation_speed)));
            }
            number = numberOfIcons - number
        }
    }
    if (event.custom_images.length == 1) {
        for (var i = 0; i < event.custom_images.length; i++) {
            for (var j = 0; j < numberOfIcons; j++) {
                divEffects.append(omgholiday_createCustomImageEffects(event.custom_images[i], Number(event.icon_size), Number(event.animation_speed)));
            }
        }
    } else if (event.custom_images.length > 1) {
        var number = numberOfIcons;
        for (var i = 0; i < event.custom_images.length; i++) {
            var random = omgholiday_randomInteger(0, number);
            for (var j = 0; j < random; j++) {
                divEffects.append(omgholiday_createCustomImageEffects(event.custom_images[i], Number(event.icon_size), Number(event.animation_speed)));
            }
            number = numberOfIcons - number
        }
    }
    if (Number(event.effect_time) > 0) {
        setTimeout(function () {
            $("#holidayEffects-" + key).css("display", "none");
        }, (Number(event.effect_time) * 1000));
    }
    if (event.frames != '') {
        $("body").append(`
            <style>
                #holidayTopFrames{background-position: top left;background-repeat: repeat-x;}
                #holidayBottomFrames{background-position: top left;background-repeat: repeat-x;}
            </style>
        `);
        $(".otHolidayEffects").append(`
            <div id="holidayTopFrames"></div>
            <div id="holidayBottomFrames"></div>
        `);
        switch (event.frame_position) {
            case "2":
                $("body").append(`
                    <style>
                        #holidayBottomFrames {background-image:url(${rootlinkHolidayEffects}/assets/images/frames/bottom/${event.frames}.png)}
                    </style>
                `);
                break;
            case "3":
                $("body").append(`
                    <style>
                        #holidayBottomFrames {background-image:url(${rootlinkHolidayEffects}/assets/images/frames/bottom/${event.frames}.png)}
                        #holidayTopFrames {background-image:url(${rootlinkHolidayEffects}/assets/images/frames/top/${event.frames}.png)}
                    </style>
                `);
                break;
            default:
                $("body").append(`
                    <style>
                        #holidayTopFrames {background-image:url(${rootlinkHolidayEffects}/assets/images/frames/top/${event.frames}.png)}
                    </style>
                `);
                break;
        }
        if (Number(event.frame_time) > 0) {
            setTimeout(function () {
                $("#holidayBottomFrames").css("display", "none");
                $("#holidayTopFrames").css("display", "none");
            }, Number(event.frame_time) * 1000);
        }
    }
}

function omgholiday_renderHomepage(key, only_home) {
    var homeCss = "";
    if (only_home == 1) {
        if (omgholiday_isHome) {
            if (key < (omgholiday_events.length - 1)) {
                homeCss += "#holidayEffects-" + key + "{display:none;}";
            } else {
                homeCss += "#holidayTopFrames,#holidayBottomFrames,#holidayEffects-" + key + "{display:none;}";
            }
        }
    }
    return homeCss;
}
function omgholiday_randomInteger(low, high) {
    return low + Math.floor(Math.random() * (high - low));
}
function omgholiday_randomFloat(low, high) {
    return low + Math.random() * (high - low);
}
function omgholiday_durationValue(value) {
    return value + "s";
}
function omgholiday_pixelValue(value) {
    return value + "px";
}
function omgholiday_createIconEffects(text, iconSize, animationSpeed) {
    var spinAnimationName = (Math.random() < 0.5) ? "clockwiseSpin" : "counterclockwiseSpinAndFlip",
        spinDuration = omgholiday_durationValue(omgholiday_randomFloat(4, 8)),
        leafDelay = omgholiday_durationValue(omgholiday_randomFloat(0, 5)),
        leftStyle = omgholiday_pixelValue(omgholiday_randomInteger(0, $(window).width())),
        html = `
            <div style="top:-${iconSize}px;left:${leftStyle};animation-name:fade,drop;animation-duration:${animationSpeed}s,${animationSpeed}s;animation-delay:${leafDelay},${leafDelay}">
                <i class="fa fa-${text}" aria-hidden="true" style="animation-name:${spinAnimationName};animation-duration:${spinDuration}"></i>
            </div>
        `;
    return html;
}
function omgholiday_createImageEffects(text, iconSize, animationSpeed) {
    iconSize = iconSize + 50;
    var spinAnimationName = (Math.random() < 0.5) ? "clockwiseSpin" : "counterclockwiseSpinAndFlip",
        spinDuration = omgholiday_durationValue(omgholiday_randomFloat(4, 8)),
        leafDelay = omgholiday_durationValue(omgholiday_randomFloat(0, 5)),
        leftStyle = omgholiday_pixelValue(omgholiday_randomInteger(0, $(window).width())),
        html = `
            <div style="top:-${iconSize}px;left:${leftStyle};animation-name:fade,drop;animation-duration:${animationSpeed}s,${animationSpeed}s;animation-delay:${leafDelay},${leafDelay}">
                <img src="${rootlinkHolidayEffects}/assets/images/images/${text}.png" style="animation-name:${spinAnimationName};animation-duration:${spinDuration}">
            </div>
        `;
    return html;
}
function omgholiday_createCustomImageEffects(url, iconSize, animationSpeed) {
    iconSize = iconSize + 50;
    var spinAnimationName = (Math.random() < 0.5) ? "clockwiseSpin" : "counterclockwiseSpinAndFlip",
        spinDuration = omgholiday_durationValue(omgholiday_randomFloat(4, 8)),
        leafDelay = omgholiday_durationValue(omgholiday_randomFloat(0, 5)),
        html = `
            <div style="top:-${iconSize}px;left:${leftStyle};animation-name:fade,drop;animation-duration:${animationSpeed}s,${animationSpeed}s;animation-delay:${leafDelay},${leafDelay}">
                <img src="${url}" style="animation-name:${spinAnimationName};animation-duration:${spinDuration}">
            </div>
        `;
    return html;
}