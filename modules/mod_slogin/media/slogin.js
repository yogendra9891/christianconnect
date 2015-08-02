var SLogin = SLogin || {

    initialize:function () {
        var block = document.getElementById('slogin-buttons');
        if (block === null) return;
        var elements = block.getElementsByTagName('a');
        var params = "resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes"
        for (var i = 0; i < elements.length; i++) {
            elements[i].onclick = function (e) {
                if (typeof(PopUpWindow) == 'window') {
                    PopUpWindow.close();
                }
                var el = this.getElementsByTagName('span');
                var size = SLogin.getPopUpSize(el[0].className);
                var win_size = SLogin.WindowSize();
                var centerWidth = (win_size.width - size.width) / 2;
                var centerHeight = (win_size.height - size.height) / 2;
                var PopUpWindow = window.open(
                    this.href,
                    'LoginPopUp',
                    'width=' + size.width
                        + ',height=' + size.height
                        + ',left=' + centerWidth
                        + ',top=' + centerHeight
                        + ',' + params
                );
                PopUpWindow.focus();
                return false;
            }

        }

    },

    WindowSize:function () {
        var myWidth = 0, myHeight = 0, size = {width:0, height:0};
        if (typeof( window.innerWidth ) == 'number') {
            //Non-IE
            myWidth = window.innerWidth;
            myHeight = window.innerHeight;
        } else if (document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight )) {
            //IE 6+ in 'standards compliant mode'
            myWidth = document.documentElement.clientWidth;
            myHeight = document.documentElement.clientHeight;
        } else if (document.body && ( document.body.clientWidth || document.body.clientHeight )) {
            //IE 4 compatible
            myWidth = document.body.clientWidth;
            myHeight = document.body.clientHeight;
        }
        size.width = myWidth;
        size.height = myHeight;

        return size;
    },

    getPopUpSize:function (el) {
        var size = {width:0, height:0};

        switch (el) {
            case 'vkontakteslogin':
                size = {width:585, height:350};
                break;
            case 'googleslogin':
                size = {width:450, height:350};
                break;
            case 'facebookslogin':
                size = {width:900, height:550};
                break;
            case 'twitterslogin':
                size = {width:450, height:550};
                break;
            case 'yandexslogin':
                size = {width:350, height:450};
                break;
            case 'linkedinslogin':
                size = {width:350, height:450};
                break;
            case 'odnoklassnikislogin':
                size = {width:550, height:250};
                break;
            case 'mailslogin':
                size = {width:450, height:325};
                break;
            default:
                size = {width:900, height:550};
                break;
        }

        return size;
    },

    addListener:function (obj, type, listener) {
        if (obj.addEventListener) {
            obj.addEventListener(type, listener, false);
            return true;
        } else if (obj.attachEvent) {
            obj.attachEvent('on' + type, listener);
            return true;
        }
        return false;
    }

};

SLogin.addListener(window, 'load', function () {
    SLogin.initialize();
});