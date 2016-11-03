<?php
/**
 * Created by PhpStorm.
 * User: misaka
 * Date: 2016-11-3
 * Time: 14:00
 */
require '../../x/mysql.class.php';
$r = $sql->getData('SELECT ANY_VALUE(aid) aid,ANY_VALUE(album) album, ANY_VALUE(img) img FROM `imouto_music` GROUP BY `aid` ORDER BY `aid` DESC,RAND() LIMIT 10');
$r = array_map(function ($o) {
    $o['rid'] = $o['aid'];
    unset($o['aid']);
    $o['title'] = $o['album'];
    unset($o['album']);
    return $o;
}, $r);
$list = json_encode($r);
?>
<style>
    #sR .close {
        font: bold 40px/1 Arial;
        width: 40px;
        position: absolute;
        z-index: 2;
        bottom: 14px;
        left: 14px;
    }

    #sR .close:hover {
        opacity: 1;
    }

    #btn-sR {
        position: absolute;
        bottom: 1em;
        left: 1em;
        opacity: .5;
        cursor: pointer;
    }

    #btn-sR svg {
        height: 40px;
        width: 40px;
        display: block;
    }

    #btn-sR:hover {
        opacity: 1;
    }

    #sR {
        position: absolute;
        top: 0;
        right: 100%;
        z-index: 3;
        width: 350px;
        height: 100%;
        overflow: hidden;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        line-height: 2em;
        font-size: 48px;
        background: #000;
        color: #FFF;
        text-shadow: 1px 3px 0 rgba(0, 0, 0, .5);
    }

    #sR li {
        display: block;
        cursor: pointer;
        position: relative;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /*#sR{position:absolute;top:0;left:0;z-index:3;
        width:100%;height:100%;overflow:hidden;
        overflow-y:auto;-webkit-overflow-scrolling:touch;
        line-height:2em;font-size:80px;
        background:#111;background:rgba(0,0,0,.9);color:#FFF;
        text-shadow:1px 3px 0 rgba(0,0,0,.5);}

    #sR li{
        float:left;
        width:100%;
        cursor:pointer;
        position:relative;
        -moz-box-sizing:border-box;
        box-sizing:border-box;
    }*/
    #sR ul li b {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: no-repeat 50% 50%;
        background-size: cover;
        opacity: .3;
    }

    #sR ul li.a b,
    #sR ul li:hover b {
        opacity: 1;
    }

    #sR li.a:after {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        margin-top: -15px;
        border: 15px solid rgba(0, 0, 0, 0);
        border-left-color: #FFF;
    }

    #sR li h4 {
        padding: 0 .5em;
        position: relative;
        z-index: 1;
    }

    /*@media(min-width:800px){
        #sR li{width:49.98%;}
    }
    @media(min-width:1600px){
        #sR li{width:33.33%;}
    }
    @media(min-width:2400px){
        #sR li{width:25%;}
    }*/
    body {
        transition: left .3s ease, -webkit-transform .3s ease;
        transition: transform .3s ease;
    }

    #btn-sR {
        transition: .3s ease;
    }

    #sR, #sR li, #sR li b {
        transition: opacity .3s ease;
    }
</style>
<a id="btn-sR">
    <svg>
        <rect x="6" y="9" rx="2" ry="2" width="28" height="5"/>
        <rect x="6" y="18" rx="2" ry="2" width="28" height="5"/>
        <rect x="6" y="27" rx="2" ry="2" width="28" height="5"/>
    </svg>
</a>
<div id="sR">
    <span class="close"><!-- × --></span>
    <ul></ul>
</div>
<script>
    window.setRid = function (doc) {
        var o = document.getElementById('sR'),
            rid = localStorage.getItem('rid') || 0,
            body = document.body,
            R = <?php echo $list; ?>,
            hide = function () {
                body.style.cssText = '';
            },
            show = function () {
                body.style.cssText = '-webkit-transform:translateX(350px);transform:translateX(350px);';
                /*left:350px;*/
            },
            cut = function () {
                if (body.style.cssText) {
                    hide()
                } else {
                    show()
                }
            };
        document.getElementById('btn-sR').onclick = cut;
        for (var h = '', i = 0; i < R.length; i++) {
            h += '<li onclick="setRid(' + R[i].rid + ')"><b style="background-image:url(' + R[i].img + ');background-size:440px 188px"></b><h4>' + R[i].title + '</h4></li>'
        }
        o.getElementsByTagName('span')[0].onclick = hide;
        o.getElementsByTagName('ul')[0].innerHTML = h;
        var rid = parseInt(localStorage.getItem('rid') || 0);
        var oldRLi;
        var Rli = o.getElementsByTagName('li');
        oldRLi = Rli[rid];
        oldRLi.className = 'a';
        o.onmousewheel =
            o.ontouchmove = function (e) {
                e.stopPropagation()
            };
        if (window.fm) {
            fm.rid = rid
        }
        var setRid = function (rid) {
            hide();
            if (fm.rid == rid) {
                return
            }
            localStorage.setItem('rid', rid);
            oldRLi.className = '';
            Rli[rid].className = 'a';
            oldRLi = Rli[rid];
            if (window.fm) {
                fm.rid = rid;
                fm.clear()
            } else {
                console.log(rid)
            }
        };
        setRid.cut = cut;
        return setRid
    }(document);
</script>
