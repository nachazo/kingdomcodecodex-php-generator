</div>

<div id="divmapiframe" style="display:none;background-color:white;">
    <div class="titleiframe">
        Kingdom Come: Deliverance online map
        <div id="linkiframe"><a id="linkiframesub" target="_blank"></a></div>
        <div class="close-button">X</div>
    </div>
    <div>
        <iframe id="mapiframe" style="width:100%;height:100%"></iframe>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    loadLang();
});
$(window).on("unload", function(e) {
    var entry = $('#main').find('#entry:visible:first').attr("class");
    if (entry) {
        var entry = entry.split(" ");
        if (entry[1]) {
            if (entry[1].indexOf("i_")>0) {
                Cookies.set('kcd_codex_last_read', entry[1]);
            }
        }
    }
});

var firstUrlClean = true;

var validLangs = ['en', 'es', 'ru', 'fr', 'de', 'it', 'zh', 'cs', 'pl', 'tk'];
var specialFontLang = ['ru', 'zh'];
function loadLang() {
    var lang = navigator.language || navigator.userLanguage;
    lang = lang.split("-");
    lang = lang[0];
    if (Cookies.get("kcd_codex_lang")) {
        lang = Cookies.get("kcd_codex_lang");
    }
    if (!lang) lang = "en";

    var getVars = getUrlVars();
    if (getVars['lang']) {
        lang = getVars['lang'];
    }

    if (!myInArray(lang, validLangs)) {
        lang = "en";
    }
    $("#codex-me").load("lang/"+lang+".html", function() {
        startThings();
    });

    if (myInArray(lang, specialFontLang)) {
        $(document.body).addClass("specialChar");
    } else {
        $(document.body).removeClass("specialChar");
    }
    return true;
}
function changeLang(lang) {
    if (!lang) lang = "en";
    if (!myInArray(lang, validLangs)) {
        lang = "en";
    }
    $("#codex-me").empty();
    Cookies.set('kcd_codex_lang', lang);
    return loadLang(lang);
}

function myInArray(what, arr)
{
    if (arr && what) {
        for (i = 0; i < arr.length; i++) { 
            if (arr[i]==what) {
                return true;
            }
        }
    }
    return false;
}

function startThings()
{
    lazyload();

    var getVars = getUrlVars();

    if (directLoadEntry()) {
        showEntry(directLoadEntry());
    } else {
        if (Cookies.get("kcd_codex_last_read")) {
            showEntry(Cookies.get("kcd_codex_last_read"));
            Cookies.remove("kcd_codex_last_read");
        } else {
            showOnly("welcome");
            if(window.innerWidth <= 768) {
                
            } else {
                setTimeout('openNav()', 500);
            }
        }
    }

    $('.sidenav').scroll(function() { 
        $('.closebtn').css('top', $(this).scrollTop());
        var total = $(this).scrollTop();
        total = total+32;
        $('.resetbtn').css('top', total);        
    });

    $(".codex-icons a").each(function() {
        $(this).hover(
            function() {
                $(this).addClass("opacity-100");
            }, function() {
                $(this).removeClass("opacity-100");
            }
        );
    });

    $("img.lazyload").click(function() {
        if ($(this).hasClass("no-zoom")) return true;
        if ($(this).css("max-width")=="50%") {
            $(this).animate({"width":"100%","max-width":"100%"}, 250);
        } else if ($(this).css("max-width")=="100%") {
            $(this).animate({"width":"50%","max-width":"50%"}, 250);
        }        
    });

    if(window.innerWidth >= 768) {
        $('.masterTooltip').hover(function(){
                // Hover over code
                var title = $(this).attr('title');
                $(this).data('tipText', title).removeAttr('title');
                $('<p class="tooltip"></p>')
                .text(title)
                .appendTo('body')
                .fadeIn('fast');
                $(document.body).css("overflow-x", "hidden");
        }, function() {
                // Hover out code
                $(this).attr('title', $(this).data('tipText'));
                $('.tooltip').remove();
                $(document.body).css("overflow-x", "");
        }).mousemove(function(e) {
                var mousex = e.pageX + 7; //Get X coordinates
                var mousey = e.pageY + 3; //Get Y coordinates
                $('.tooltip')
                .css({ top: mousey, left: mousex })
        });
    }

    $('.codex-icons a').hover(function(){
        $(this).find('.codex-icons a').fadeToggle(500);
    });

    if (Cookies.get('kcd_codex_width')) {
        $("div#entry").css("max-width", Cookies.get('kcd_codex_width'));
    }
    
    $("#escudo").dblclick(function() {
        showAll();
    });

    limpiarTeclasTutorial();

    if (getVars['clean']=="true") {
        $("#opennav").hide();
        $(".codex-icons").hide();
        $("div#nav-controls").hide()
        $(".permalink").hide();
        $("#main").css("margin-top", "24px");
        $(".open-in-map").hide();
    }

    spoilerButtons();
}

function getUrlVars()
{
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
    vars[key] = value;
    });
    return vars;
}

function removeVarsInUrl()
{
    var uri = window.location.toString();
    if (uri.indexOf("?") > 0) {
        var clean_uri = uri.substring(0, uri.indexOf("?"));
        window.history.replaceState({}, document.title, clean_uri);
    } else if (uri.indexOf("#") > 0) {
        var clean_uri = uri.substring(0, uri.indexOf("#"));
        window.history.replaceState({}, document.title, clean_uri);
    }
}

function addVarInUrl(data)
{
    removeVarsInUrl();
    if (data) {
        window.history.replaceState({}, document.title, window.location.toString()+"?entry="+data);
    } else {
        window.history.replaceState({}, document.title, window.location.toString());
    }
}

function cleanUrl()
{
    var direct = decodeURIComponent(window.location);
    if (direct.indexOf("?")>0) {
        return direct.split("?")[0];
    } else if (direct.indexOf("#")>0) {
        return direct.split("#")[0];
    } else {
        return direct;
    }
    return false;
}

function directLoadEntry()
{
    if (decodeURIComponent(window.location).indexOf("#")>0) {
        var direct = decodeURIComponent(window.location).split("#");
        if ($.isArray(direct) && direct[1]) {
            if ($("div#entry."+direct[1]).length) {
                return direct[1];
            }
        }
    } else {
        var getVars = getUrlVars();
        if (getVars['entry'] && $("div#entry."+getVars['entry']).length) {
            return getVars['entry'];
        }
    }    
    return false;
}

function permalink(data)
{
    if (data) {
        prompt("", cleanUrl()+"?entry="+data);
    }
    return false;
}

function plusWidth()
{
    $("div#entry").each(function() {
        var mw = parseInt( $(this).css("max-width"), 10);
        if (mw>=35 && mw!=100) {
            mw = mw + 15;
            //$(this).css("max-width", mw+"%");
			$(this).animate({
				maxWidth: mw+"%",
			}, 150);
            Cookies.set('kcd_codex_width', $(this).css("max-width"));
        }
    });
}

function minusWidth()
{
    $("div#entry").each(function() {
        var mw = parseInt( $(this).css("max-width"), 10);
        if (mw<=50) return false;
        mw = mw - 15;
        //$(this).css("max-width", mw+"%");
        $(this).animate({
			maxWidth: mw+"%",
		}, 150);
        Cookies.set('kcd_codex_width', $(this).css("max-width"));
    });
}

var removeKeys = ['ui_open_inventory','xi_move_help', 'ui_open_map', 'ui_controller_shoulder_r1', 'ui_controller_shoulder_l1', 'ui_dp_up', 'ui_dp_down', 'xi_lock_dir_help', 'ui_dp_left', 'ui_dp_right', 'xi_sword_position_r','xi_sword_rotation','xi_sword_pressure','g_xi_modifier','g_xi_activate','attack1'];
function limpiarTeclasTutorial()
{
    $("div#entry.tutorials span.key").each(function() {
        var texto = $(this).text();
        if (jQuery.inArray(texto, removeKeys) !== -1) {
            $(this).remove();
        }
    });
}

function despliega(cat) {
    var c = 0;
    $(".menu-item").each(function() {
        if ($(this).hasClass("cat_"+cat) && $(this).is(":visible")) {
            c++;
        }
    });
    if (c>0) {
        $(".menu-item").each(function() {
            $(this).hide();
        });
    } else {
        $(".menu-item").each(function() {
            $(this).hide();
            if ($(this).hasClass("cat_"+cat)) {
                $(this).show();
                var aux = $(this).attr("id").replace("menu_item_", "");
                statusReadList(aux);
            }
        });
    }
}
function despliega_force(cat) {
    $(".menu-item").each(function() {
        $(this).hide();
        if ($(this).hasClass("cat_"+cat)) {
            $(this).show();
            var aux = $(this).attr("id").replace("menu_item_", "");
            statusReadList(aux);
        }
    });
}
function cual_cat()
{
    if ($(".title a.category-item.selected").attr("id")) {
        return $(".title a.category-item.selected").attr("id").replace("category_item_", "");        
    }
}
function cual_item()
{
    if ($("#menu a.menu-item.selected").attr("id")) {
        return $("#menu a.menu-item.selected").attr("id");
    }
}
function colorize(cual, cat) {
    $(".menu-item").each(function() {
        $(this).removeClass("selected");
    });
    $(".menu-item").each(function() {
        if ($(this).attr("id")=="menu_item_"+cual) {
            $(this).addClass("selected");
        }
    });

    $(".category-item").each(function() {
        $(this).removeClass("selected");
    });
    $(".category-item").each(function() {
        if ($(this).attr("id")=="category_item_"+cat) {
            $(this).addClass("selected");
        }
    });

    $(".codex-icons a").each(function() {
        $(this).removeClass("opacity-50");
    });
    $(".codex-icons a").each(function() {
        if ($(this).attr("id")!="icon-codex-"+cat) {
            $(this).addClass("opacity-50");
        }
    });
    despliega(cat);
}
function reset() {
    $(".menu-item").each(function() {
        $(this).hide();
    });
    $("#search").val("");
    showOnly(null);
    showEntry(null, null);
    showOnly("welcome");
}
function showEntry(entry, category) {
    $("div#entry").each(function() {
        $(this).hide();
        if ($(this).hasClass(entry)) {
            showNavControls();
            $(this).fadeIn("fast");
            statusRead(entry);
            statusReadList(entry);
            if (!category) {
                var category = $(this).attr("class").split(" ");
                category = category[0];
            }
            colorize(entry, category);
            closeNav();
            $(window).scrollTop(0);
            if (firstUrlClean==true) {
                firstUrlClean = false;
                return true;
            }
            addVarInUrl(entry);
        }
    });
}
function showOnly(cat) {
    hideNavControls();
    $("div#entry").each(function() {
        $(this).hide();       
        if ($(this).hasClass(cat)) {
            colorize(null, cat);
            $(this).fadeIn("fast");
        }
    });
    addVarInUrl();
    closeNav();
    $(window).scrollTop(0);
}
function showAll() {
    $('div#entry').show("fast");
    colorize(null, null);
    hideNavControls();
    closeNav();
}
function hideNavControls() {
    $("div#nav-controls").each(function() {
        $(this).hide();
    });
}
function showNavControls() {
    $("div#nav-controls").each(function() {
        $(this).show();
    });
}
function nav() {
    if ($("#menu").css("width")=="0px") {
        openNav();
    } else {
        closeNav();
    }
}
function openNav() {
    if ($("#menu").length==0)  return false;
    if(window.innerWidth <= 768) {
        $("#menu").css('width', '100%');
    } else {
        $("#menu").css('width', '<?=$menu_width?>');
    }    
    $("#main").css('margin-left', '<?=$menu_width?>');
    var cual = cual_cat();
    if (cual) {
        despliega_force(cual);    
        setTimeout('$("#menu").scrollTo("#"+cual_item(),200, 150);', 550);
    }
}
jQuery.fn.scrollTo = function(elem, speed, opt) {
    var total = $(this).scrollTop() - $(this).offset().top + $(elem).offset().top - opt;
    if (total<0) return this;
    $(this).animate({
        scrollTop: total
    }, speed == undefined ? 1000 : speed); 
    return this; 
};
function closeNav() {
    $("#menu").css('width', '0px');
    $("#main").css('margin-left', '0px');
}
function search(element) {
    var value = $(element).val().toLowerCase();
    if (value=="") {
        $(".menu-item").each(function() {
            $(this).hide();
        });
    } else {
        $("#menu .menu-item").each(function () {
            var esteTexto = $(this).text().toLowerCase();
            if (esteTexto.search(value) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
}

function statusReadList(id)
{
    $("menu_item_"+id+"").removeClass("list-as-read");
    $("#menu_item_"+id+"").removeClass("list-as-unread");
    if (isRead(id)) {
        $("#menu_item_"+id+"").addClass("list-as-unread");
    } else {
        $("#menu_item_"+id+"").removeClass("list-as-unread");
    }
}

function statusRead(id)
{
    if (isRead(id)) {
        $("#entry."+id+" #read .mark-as-read").hide();
        $("#entry."+id+" #read .mark-as-unread").show();
    } else {
        $("#entry."+id+" #read .mark-as-read").show();
        $("#entry."+id+" #read .mark-as-unread").hide();
    }
}

function markAllAsRead(category)
{
    $(".menu-item").each(function() {
        if ($(this).hasClass("cat_"+category)) {
            var aux = $(this).attr("id").replace("menu_item_", "");
            if (isRead(aux)) {                
                markAsUnread(aux);
            } else {
                markAsRead(aux);
            }
        }
    });
}

function markAsRead(id)
{
    Cookies.set('kcd_codex_'+id, 'read');
    $("#entry."+id+" #read .mark-as-read").hide();
    $("#entry."+id+" #read .mark-as-unread").show();
    statusReadList(id);
}

function markAsUnread(id)
{
    Cookies.remove('kcd_codex_'+id);
    $("#entry."+id+" #read .mark-as-read").show();
    $("#entry."+id+" #read .mark-as-unread").hide();
    statusReadList(id);
}

function isRead(id)
{
    if (Cookies.get('kcd_codex_'+id)=="read") {
        return true;
    }
    return false;
}

function next(id)
{
    id = "menu_item_"+id;
    var cual = $('#menu #'+id).nextAll('a').attr("id");
    if (cual) {
        cual = cual.replace("menu_item_", "");
        showEntry(cual);
    }
}

function previous(id)
{
    id = "menu_item_"+id;
    var cual = $('#menu #'+id).prevAll('a').attr("id");
    if (cual) {
        cual = cual.replace("menu_item_", "");
        showEntry(cual);
    }
}

function deleteAllCookies()
{
    var have = false;
    $.each(Cookies.get(), function( k, v ) {
        if (k.indexOf("kcd_") >= 0) {
            Cookies.remove(k);
            have = true;
        }
    });
    if (have == true) {
        $(".cookie-delete-all").append(" &nbsp; <span>Ok!</span>");
    }
}

function spoilerButtons()
{
    $("#yes-this").click(function() {
        $("#aviso-spoiler").hide();
        return openInMap(aux_x, aux_y, aux_zoom, false);
    });
    $("#yes-always").click(function() {
        Cookies.set('kcd_codex_spoiler_accept', true);
        $("#aviso-spoiler").hide();
        return openInMap(aux_x, aux_y, aux_zoom, false);
    });
    $('#yes-this, #yes-always, #no-never').each(function(){
        $(this).hover(
            function() {
                $(this).css("border", "2px solid #700809");
                $(this).css("opacity", "1");
            }, function() {
                $(this).css("border", "2px solid #555");
                $(this).css("opacity", "0.9");
            }
        );
    });
}

var aux_x;
var aux_y;
var aux_zoom;

function openInMap(x, y, zoom, spoiler)
{
    aux_x = null;
    aux_y = null;
    aux_zoom = null;
    if (x && y) {
        if (spoiler==true && Cookies.get('kcd_codex_spoiler_accept')!="true") {
            aux_x = x;
            aux_y = y;
            aux_zoom = zoom;
            $(document.body).append('<a href="#aviso-spoiler" id="tmplinks"></a>');
            if(window.innerWidth <= 768) {
                var topPos = 15;
            } else {
                var topPos = 50;
            }
            $("#tmplinks").leanModal({ top: topPos, closeButton: "#no-never" });
            $("#tmplinks").click();
            $("#tmplinks").remove();
            return false;
        }

        //var link = '<?=$kcdMapLink?>#'+zoom+'/'+y+'.0/'+x+'.0';
        //var link = '<?=$kcdMapLink?>?marker='+y+','+x+'';
        //var link = '<?=$kcdMapLink?>?marker=null#'+zoom+'/'+x+'.0/'+y+'.0';
        //window.open(link, '_blank');
        $("#mapiframe").attr("src", "");
        var link = '<?=$kcdMapLink?>?marker='+y+','+x+'&zoom='+zoom+'&sidebar=false&popup=false';        
        $(document.body).append('<a href="#divmapiframe" id="tmplink"></a>');
        if(window.innerWidth <= 768) {
            var topPos = 0;
        } else {
            var topPos = 50;
        }
        $("#tmplink").leanModal({ top: topPos, closeButton: ".close-button" });
        $("#mapiframe").attr("src", link);
        $("#linkiframesub").attr("href", link);
        $("#linkiframesub").text(link);
        $("#tmplink").click();
        $("#tmplink").remove();
        if(window.innerWidth <= 768) {
            $("#divmapiframe").css("left", "0");
            $("#divmapiframe").css("margin-left", "0");
            $("#divmapiframe").css("width", "100%");
            $("#divmapiframe").css("height", "93%");
        }
    }
}
</script>

<script async src="https://www.googletagmanager.com/gtag/js?id=UA-689574-4"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'UA-689574-4');
</script>

</body>
</html>