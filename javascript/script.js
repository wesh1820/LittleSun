// $(document).ready(function() {
//     var sidebar = $("#sidebar");
//     var closeBtn = $("#closeBtn");
//     var hamburgerMenu = $(".hamburger-menu");

//     closeBtn.on("click", function() {
//         sidebar.removeClass("open");
//     });

//     hamburgerMenu.on("click", function() {
//         sidebar.addClass("open");
//     });

//     $(document).click(function(event) {
//         if (!$(event.target).closest('#sidebar, .hamburger-menu').length) {
//             sidebar.removeClass("open");
//         }
//     });

//     $(".add-button").click(function() {
//         $("#popup-content").load("add_user.php");
//         $("#myModal").css("display", "block");
//     });

//     $(".close, .modal").click(function() {
//         $("#myModal").css("display", "none");
//     });

//     $(".modal-content").click(function(event) {
//         event.stopPropagation();
//     });
// });
