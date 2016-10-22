<?php
date_default_timezone_set('Asia/Shanghai');
function fileHash($filename){
	echo hash_file('md5', $filename);
}
?>
<!DOCTYPE html>
<script>
with(location){if(protocol=='https:'||search){/*href='http://itorr.sinaapp.com/fm/'+hash*/href='http://kloli.tk/fm/'+hash}}
/*!function(l,u,r){with(l){if(href.match(/itorr/)&&(origin!=u||search)){setTimeout(function(){cNz('J:'+r);},2000);href=u+hash}}}(location,'http://fm.itorr.sinaapp.com',document.referrer)*/
</script>
<meta charset="utf-8">
<title>偷揉FM</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="user-scalable=no,width=1024">
<!-- <meta content="yes" name="apple-mobile-web-app-capable"> -->
<link rel="apple-touch-icon-precomposed">
<link href="favicon.ico" rel="shortcut icon">
<link rel="stylesheet" type="text/css" href="i/style.css?h=<?php fileHash('i/style.css'); ?>">
<!-- i/style.css?h=<?php fileHash('i/style.css'); ?> -->
<div id="box">
	<div class="cover">
		<img class="h">
		<div class="info">
			<h1>偷揉电台</h1>
			<p>可发送弹幕的ACG电台!</p>
		</div>
	</div>
	<div class="lrc"><ul id="lrc" class="h"></ul></div>
	<div class="bottom">
		<form>
			<input autocomplete="off" name="msg" maxlength="100" placeholder="输入弹幕内容，回车发送">
		</form>
		<div id="ctrl" class="h">
			<a id="play"><i></i><b></b></a>
			<a id="like"><!--❤-->
<svg viewBox="0 0 80 80"><path fill="currentColor" d="M57.324,15.028c-9.671,0-17.273,6.312-17.273,15.3c0-8.988-7.807-15.3-17.479-15.3
	c-10.738,0-16.76,8.157-16.76,17.797c0,26.176,34.239,27.56,34.239,44.147c0-16.587,34.136-17.971,34.136-44.147
	C74.188,23.185,68.062,15.028,57.324,15.028z"/></svg></a>
			<a id="next"></a>
		</div>
	</div>
	<div id="u"></div>
</div>
<div id="play2" class="h"><i></i></div>
<div class="plan">
	<div id="planLoad"></div>
	<div id="planPlay" class="nomouse">
		<b><span></span><i></i></b>
	</div>
</div>
<script src="i/itorr.m.js?h=<?php fileHash('i/itorr.m.js'); ?>"></script>
<!-- i/itorr.m.js?h=<?php fileHash('i/itorr.m.js'); ?> -->
<script>
String.prototype.enTxt=function(){
	return this.replace(/(^\s*)|(\s*$)/g,'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/ /g,'&nbsp;').replace(/\'/g,'&#39;').replace(/\"/g,'&quot;')
};
var x=function(arg){
	arg=Array.prototype.slice.apply(arguments);
	if(arg[0].match(/^x\//))
		arg[0]='http://itorr.sinaapp.com/fm/'+arg[0];
	return $.x.apply(this,arg)
},
fm=function(win,doc){
	var A=new Audio(),
	list={},
	run=0,
	playList=[],
	_img=$('img'),
	_u=function(str){
		if(str.match(/http:\/\//))
			return str;
		for(var _a=parseInt(str),_n=str.substr(1),_c=Math.floor(_n.length/_a),_y=_n.length%_a,_s=new Array(),i=0;i<_y;i++)
			_s[i]=_n.substr((_c+1)*i,_c+1);
		for(i=_y;i<_a;i++)
			_s[i]=_n.substr(_c*(i-_y)+(_c+1)*_y,_c);
		for(i=0,_c=_n='';i<_s[0].length;i++)
			for(j=0;j<_s.length;j++)
				_c+=_s[j].substr(i,1);
		for(i=0,_c=unescape(_c);i<_c.length;i++)
			_c.substr(i,1)=='^'?_n+='0':_n+=_c.substr(i,1);
		return _n
	},
	add=function(i){
		x((fm.rid<11?'x/?a=radio&rid=':'music.163.php?a=radio&rid=')+fm.rid+'&_r='+Math.random(),function(r){
			for(var l=r.length,i=0;i<l;i++){
				/*list=list.concat(r);*/
				list[r[i].xid]=r[i];
				playList.push(r[i].xid)
			}
			if($('#ctrl').className!='h load'){
				setTimeout(function(){
					$('#ctrl').className='';
				},3e3);
				$('#next').href='#!'+playList[0]
			}else{
				$('#ctrl').className=''
			}
			/*if(!run)fm.play(list[playList[0]]);*/
			if(!run)fm.next()
		})
	},
	fm={
		A:A,
		rid:0,
		clear:function(){
			list={};
			playList=[];
			run=0;
			add()
		},
		play:function(M){
			$('#ctrl').className='h';
			$('#play').className='p';
			if(run&&!navigator.userAgent.match(/ip(ad|hone)/i))
				$('#play2').className='h';
			var vvv;
			vvv=M.img.match(/http:\/\//)?M.img:'http://img.xiami.net/images/album/'+M.img;
			if(vvv!=_img.src){
				_img.className='h';
				_img.src=vvv;
				$('[rel="shortcut icon"]').href=$('[rel="apple-touch-icon-precomposed"]').href=vvv
			}
			/*.replace(/_(3|2|1)\./,'_4.');*/
			$('h1').innerHTML=document.title=M.title;
			$('p').innerHTML=M.artist/*+(M.lrc||'')*/;
			/*$('span').innerHTML=M.play;*/
			A.src=_u(M.mp3);
			/*new Image().src='http://www.mouto.org/down.php?did='+encodeURIComponent(A.src);*/
			A.play();
			run=1;
			if(_img.complete)
				setTimeout(_img.onload,10);
			if(win.dm)
				dm.load(M.xid);
			if(win.lrc)
				lrc.load(M.xid);
			/*判断当前是否喜欢这首曲子*/
			if(win.U)
				U.iflike(M.xid,function(r){
					$('#like').className=r?'a':''
				});
			/*fix 判断即将播放的曲子是否在待播放列表首位 */
			if(playList[0]==M.xid)
				playList.shift();
			/*如果待播放列表还有歌曲 就显示下一首 不然 隐藏*/
			setTimeout(function(){
				if(playList.length){
					$('#ctrl').className='';
					$('#next').href='#!'+playList[0]
				}else{
					$('#ctrl').className='h load'
				}
			},3e3);
			/*如果待播放列表剩余不及3首 那么载入更多*/
			if(playList.length<3)
				add()
		},
		song:function(i){
			var f=function(r){
				/*playList.push(r.xid);*/
				fm.play(r)
			};
			if(list[i])
				f(list[i])
			else
				x((fm.rid<11?'x/?a=song&id=':'music.163.php?a=song&id=')+i+'&_r='+Math.random(),function(r){
					if(r.error){
						alert(r.error);
						return add()
					}
					for(var l=r.length,i=0;i<l;i++){
						list[r[i].xid]=r[i];
						if(i!=0)
							playList.push(r[i].xid)
					}
					/*console.log(playList);*/
					f(r[0])
				})
		},
		log:function(pid){
            x('log.php', 'rid='+fm.rid+'&pid='+pid, function(r){
                console.log(r)
            })
        },
        next:function(){
        	fm.log(location.hash.match(/[\d]+/));
        	setTimeout(function(){
				location.href=$('#next').href
        	},3e3)
		},
		time:function(){
			return A.currentTime*1000;
		},
		adjust:function(i){
			i=i||1;
			A.currentTime+=i*5;
		}
	};
	$.j('i/plan.js?h=<?php fileHash('i/plan.js'); ?>');
	/*i/plan.js?h=<?php fileHash('i/plan.js'); ?>*/
	$('#play').onclick=function(){
		if(A.paused){
			A.play();
			$('#play').className='p';
			$('#play2').className='h'
		}else{
			A.pause();
			$('#play').className='play';
			$('#play2').className=''
		}
	};
	$('#like').onclick=function(){
		if(!U.me)
			return alert('尚未完成登录');
		if($('#like').className=='a')
			return;
		if(win.dm)
			dm.send('like');
		$('#like').className='a';
		x('x/?a=like','xid='+location.hash.match(/[\d]+/),function(r){
			/*console.log(r);*/
			if(r.error)
				return alert(r.error);
			/*$('#like').className='a'*/
		});
	};
	$('img').onload=function(){
		this.className=''
	};
	$('.info p').onclick=function(){
		$('input[name="k"]').value=this.innerHTML;
		$('#showS').click()
	};
	$('#play2').onclick=$('#play').onclick;
	var UA=navigator.userAgent;
	if(UA.match(/ip(ad|hone)/i))
		$('#play2').className='';
	$('meta[name="viewport"]').content=UA.match(/ipad/i)?'width=1024,user-scalable=no,minimal-ui':UA.match(/iphone/i)?'width=520,user-scalable=no,minimal-ui':'width=720';
	fm.rid=localStorage.getItem('rid')||0;
	var laHash='简直惨惨惨OAQ',popstate=function(){
		var lash=location.hash.substring(2);
		if('onhashchange' in win)
			win.onhashchange=popstate;
		if(laHash==location.hash)
			return;
		if(lash.match(/\&rid=[0-9]+/))
			localStorage.setItem(rid,lash.match(/\&rid=([0-9]+)/)[1]);
		if(lash.match(/[0-9]{5,20}/))
			fm.song(lash);
		else if(!run)
			add();
		laHash=location.hash
	};
	setTimeout(popstate,100);
	if(!'onhashchange' in win)
		setInterval(function(){
			if(laHash!=location.hash){
				popstate();
				laHash=location.hash;
			}
		},100);
	/*console.log('「偷揉FM v7」<http://itorr.sinaapp.com/fm/> @卜卜口 于 2014/8/24');*/
	console.log('「偷揉FM v7」<http://github.com/itorr/itorr.fm> @卜卜口 于 2015/5/23');
	return fm;
}(window,document);
var evalHtml=function(i,hash){
	x('i/'+i+'.html?h='+hash,function(H){
		var div=$.D.m('div');
		div.innerHTML=H;
		$.D.a(div);
		eval(H.split('<script>')[1].split('<\/script>')[0]);
	});
};
$.j('i/dm.js?h=<?php fileHash('i/dm.js'); ?>');
/*i/dm.js?h=<?php fileHash('i/dm.js'); ?>*/
$.j('i/lrc.js?h=<?php fileHash('i/lrc.js'); ?>');
/*i/lrc.js?h=<?php fileHash('i/lrc.js'); ?>*/
$.j('i/u.js?h=<?php fileHash('i/u.js'); ?>');
/*i/u.js?h=<?php fileHash('i/u.js'); ?>*/
evalHtml('search','<?php fileHash('i/search.html'); ?>');
/*i/search.html?h=<?php fileHash('i/search.html'); ?>*/
setTimeout(function(){
	$.lcss('i/star.css?h=<?php fileHash('i/star.css'); ?>');
	/*i/star.css?h=<?php fileHash('i/star.css'); ?>*/
	evalHtml('fo','<?php fileHash('i/fo.html'); ?>');
	/*i/fo.html?h=<?php fileHash('i/fo.html'); ?>*/
	evalHtml('key','<?php fileHash('i/key.html'); ?>');
	/*i/key.html?h=<?php fileHash('i/key.html'); ?>*/
	evalHtml('menu','<?php fileHash('i/menu.html'); ?>');
	/*i/menu.html?h=<?php fileHash('i/menu.html'); ?>*/
	evalHtml('volume','<?php fileHash('i/volume.html'); ?>');
	/*i/volume.html?h=<?php fileHash('i/volume.html'); ?>*/
	evalHtml('rid','<?php fileHash('i/rid.html'); ?>');
	/*i/rid.html?h=<?php fileHash('i/rid.html'); ?>*/
	$.j('i/hotkey.js?h=<?php fileHash('i/hotkey.js'); ?>');
	/*i/hotkey.js?h=<?php fileHash('i/hotkey.js'); ?>*/
	$.j('i/crop.js?h=<?php fileHash('i/crop.js'); ?>');
	/*i/crop.js?h=<?php fileHash('i/crop.js'); ?>*/
	$.j('//1.mouto.org/x.js');
	$.j('i/fastclick.m.js?h=<?php fileHash('i/fastclick.m.js'); ?>',function(){
		/*i/fastclick.m.js?h=<?php fileHash('i/fastclick.m.js'); ?>*/
		FastClick.attach(document.body);
	});
},1000);
</script>
