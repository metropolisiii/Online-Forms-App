// equalizes column heights
function equalHeight(group) {
     tallest = 0;
     group.each(function() {
          thisHeight = $(this).height();
          if(thisHeight > tallest) {
               tallest = thisHeight;
          }
     });
     group.height(tallest);
}
$(document).ready(function() {
     // Mega Menu
     function addMega(){
          $(this).addClass("hovering");
     }
     function removeMega(){
          $(this).removeClass("hovering");
     }
     var megaConfig = {
        interval: 200,
        sensitivity: 4,
        over: addMega,
        timeout: 400,
        out: removeMega
     };
     $("li.mega").hoverIntent(megaConfig)
     equalHeight($(".match"));
});