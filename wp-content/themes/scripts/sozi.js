
window.addEventListener("load", function () {
console.log("sozi");
	 document.querySelector("iframe").focus();
     var frame     = document.getElementById("sozi-pelico");
     var btnPrev   = document.getElementById("btn-prev");
     var btnNext   = document.getElementById("btn-next");
     var spanTitle = document.getElementById("frame-title");

     var player = frame.contentWindow.player;

     spanTitle.innerHTML = player.currentFrame.title;

     btnPrev.addEventListener("click", function () {
         player.moveToPrevious();
     }, false);

     btnNext.addEventListener("click", function () {
         player.moveToNext();
     }, false);

     player.addListener("frameChange", function () {
         spanTitle.innerHTML = player.currentFrame.title;
     });
 }, false);