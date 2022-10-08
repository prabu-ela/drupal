jQuery( document ).ready( function () {

  function romanize (num) {
    if (isNaN(num))
        return NaN;
    var digits = String(+num).split(""),
        key = ["","C","CC","CCC","CD","D","DC","DCC","DCCC","CM",
              "","X","XX","XXX","XL","L","LX","LXX","LXXX","XC",
              "","I","II","III","IV","V","VI","VII","VIII","IX"],
        roman = "",
        i = 3;
    while (i--)
        roman = (key[+digits.pop() + (i * 10)] || "") + roman;
    return Array(+digits.join("") + 1).join("M") + roman;
  }

  var d = new Date();
  var month = d.getMonth();
  var day = d.getDate();
  var year = d.getFullYear();
  var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

  var start = new Date(d.getFullYear(), 0, 0);
  var diff = (d - start) + ((start.getTimezoneOffset() - d.getTimezoneOffset()) * 60 * 1000);
  var oneDay = 1000 * 60 * 60 * 24;
  var newDay = Math.floor(diff / oneDay);
  var vol = romanize(year - 2010);

  jQuery('.art_heading h2').text(':countArticles');
  jQuery('.header_leftsection h3:first').text(monthNames[month] + ' ' + day + ', ' + year);
  jQuery('.header_leftsection h3:last').text("Volume " + vol + ", Number " + newDay);
})
