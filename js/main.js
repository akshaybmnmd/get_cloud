$(document).ready(function () {
  function lsTest() {
    var test = "test";
    try {
      localStorage.setItem(test, test);
      localStorage.removeItem(test);
      return true;
    } catch (e) {
      return false;
    }
  }

  if (lsTest() === true) {
  } else {
    alert(
      "local storage is not available. This site may not function properly"
    );
  }

  if (localStorage.getItem("location")) {
    get_html(localStorage.getItem("location"));
  } else {
    get_html("images.php");
  }

  $(".hamburger").click(function () {
    $(".wrapper").toggleClass("collapse");
  });

  $(".sidebar").click((k) => {});

  $("li").click((k) => {
    $(".active")[0].className = "";
    k.currentTarget.children[0].className = "active";
    get_html(k.currentTarget.className + ".php");
  });
});
