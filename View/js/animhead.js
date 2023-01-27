window.requestAnimFrame = (function() {
  return window.requestAnimationFrame ||
    window.webkitRequestAnimationFrame ||
    window.mozRequestAnimationFrame ||
    window.oRequestAnimationFrame ||
    window.msRequestAnimationFrame ||
    function(callback) {
      window.setTimeout(callback, 1000/60);
    };
})();
var c = document.getElementById('animhead');
var c2 = document.getElementById('animhead2');
var d2 = c.getContext('2d');
if(c2!=undefined){
var d22 = c2.getContext('2d');
$(c).css({"display":"none","opacity":"0"}).parent().css("background-color","transparent");
}

var arr = [];
var cnt = 0;
var arr2 = [];
var cnt2 = 0;


window.addEventListener('load', resize);
window.addEventListener('resize', resize, false);


function resize() {
  c.width = w = window.innerWidth;
  c.height = h = 100;
	
	if(c2!=undefined){
		var he = $(".categoryDescription").outerHeight()+100;
	c2.width=window.innerWidth;
	c2.height=he;
	}
 // c.style.position = 'absolute';
  //c.style.left = (window.innerWidth - w) *
   // .01 + 'px';
  //c.style.top = (window.innerHeight - h) *
  //  .01 + 'px';
}




function anim() {
  cnt++;
  cnt2++;	

  if (cnt % 3) draw();
	if(c2!=undefined){
 if (cnt2 % 3) draw2();
	}
  window.requestAnimFrame(anim);
}
anim();

function draw() {
	
	var w  = window.innerWidth;
var h  = 100;
var _w = w * 0.5;
var _h = h * 3;
	

  var splot = {
    x: rng(_w - 900, _w + 900),
    y: rng(_h - 900, _h + 900),
    r: rng(20, 300),
    spX: rng(-0.5, 0.5),
    spY: rng(-0.5, 0.5),
	col: "rgb(0,0,0)"
  };

	splot.col=rndCol();
  arr.push(splot);
  while (arr.length > 100) {
    arr.shift();
  }
  d2.clearRect(0, 0, w, h);


  for (var i = 0; i < arr.length; i++) {

    splot = arr[i];
    d2.fillStyle = splot.col;
    d2.beginPath();
    d2.arc(splot.x, splot.y, splot.r, 0, Math.PI * 2, true);
    d2.shadowBlur = 80;
    d2.shadowOffsetX = 2;
    d2.shadowOffsetY = 2;
    d2.shadowColor = splot.col;
    d2.globalCompositeOperation = 'lighter';
    d2.fill();
	  

    splot.x = splot.x + splot.spX;
    splot.y = splot.y + splot.spY;
    splot.r = splot.r * 0.99;
  }
}






function draw2() {

	
	
	
var w  = window.innerWidth;
var h  = 800;
 w = $(c2).innerWidth();
 h = $(c2).innerHeight();
var _w = w * 0.3;
var _h = h * 3;
	

	
	
  var splot = {
    x: rng(_w - 900, _w + 900),
    y: rng(_h - 900, _h + 900),
    r: rng(20, 200),
    spX: rng(-1, 1),
    spY: rng(-1, 1),
	col: "rgb(0,0,0)"
  };

	splot.col=rndCol();
  arr2.push(splot);
  while (arr2.length > 100) {
    arr2.shift();
  }
  d22.clearRect(0, 0, w, h);


  for (var i = 0; i < arr2.length; i++) {

    splot = arr2[i];
    d22.fillStyle = splot.col;
    d22.beginPath();
    d22.arc(splot.x, splot.y, splot.r, 0, Math.PI * 2, true);
    d22.shadowBlur = 80;
    d22.shadowOffsetX = 2;
    d22.shadowOffsetY = 2;
    d22.shadowColor = splot.col;
    d22.globalCompositeOperation = 'lighter';
    d22.fill();
	  

    splot.x = splot.x + splot.spX;
    splot.y = splot.y + splot.spY;
    splot.r = splot.r * 0.96;
  }
}









function rndCol() {
	var cc=["rgb(39,255,255)","rgb(0,0,255)","rgb(100,0,255)","rgb(0,200,255)","rgb(0,0,255)","rgb(0,0,100)","rgb(0,0,255)","rgb(0,50,50)","rgb(0,150,100)","rgb(0,0,50)","rgb(0,152,255)"];
	
  // var r = Math.floor(Math.random() * 100);
  // var g = Math.floor(Math.random() * 100);
  // var b = Math.floor(Math.random() * 200);
  //return "rgb(" + r + "," + g + "," + b + ")";
	var n=rng(0,cc.length-1);
	return cc[n];

}

function rng(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}