(function () {
  var page = require('webpage').create(), system = require('system'), quality = system.args[5] || '70', orientation = system.args[6] || 'portrait', margin = system.args[7] || '1cm', userAgent = system.args[8] || 'Mozilla/5.0 (Macintosh; Intel Mac OS X) AppleWebKit/534.34 (KHTML, like Gecko) PhantomJS/1.9.0 (development) Safari/534.34', address, output, size;

  if (system.args.length < 3 || system.args.length > 9) {
    console.log('Usage: rasterize.js URL filename [paperwidth*paperheight|paperformat] [zoom] [orientation] [margin] [quality]');
    console.log('paper (pdf output) examples: "5in*7.5in", "10cm*20cm", "A4", "Letter"');
    console.log('image (png/jpg output) examples: "1920px" entire page, window width 1920px');
    console.log('"800px*600px" window, clipped to 800x600');
    phantom.exit(1);
  }
  else {

    /* REQUIRED FIELDS */
    address = system.args[1];
    output = system.args[2];
    page.viewportSize = { width: 1280, height: 720 };
    this.selectors = [];
    /* END REQUIRED FIELDS */

    if (system.args.length > 3 && system.args[2].substr(-4) === ".pdf") {
      size = system.args[3].split('*');
      page.paperSize = size.length === 2 ? {width: size[0], height: size[1], margin: margin}
      : {format: system.args[3], orientation: orientation, margin: margin};
    }
    else if (system.args.length > 3 && system.args[3].substr(-2) === "px") {
      size = system.args[3].split('*');
      if (size.length === 2) {
        pageWidth = parseInt(size[0], 10);
        pageHeight = parseInt(size[1], 10);
        page.viewportSize = {width: pageWidth, height: pageHeight};
        page.clipRect = {top: 0, left: 0, width: pageWidth, height: pageHeight};
      }
      else {
        // If a number received, instead of width and height,
        // Try to use best width and height by inspecting the number.
        pageWidth = parseInt(system.args[3], 10);
        pageHeight = parseInt(pageWidth * 3 / 4, 10); // it's as good an assumption as any
        page.viewportSize = {width: pageWidth, height: pageHeight};
        //if it's a non-pdf, then it doesn't use orientation or margin, so then useragent is lower in the arg list
        if (typeof system.args[6] !== 'undefined') {
          page.settings.userAgent = system.args[6];
        }
        if (typeof system.args[7] !== 'undefined') {
          page.selectors = JSON.parse(system.args[7]);
        }
      }
    }
    if (system.args.length > 4) {
      page.zoomFactor = system.args[4];
    }

    // Add better error reporting when url fails to load. 
    page.onResourceError = function (resourceError) {
      page.reason = resourceError.errorString;
      page.reason_url = resourceError.url;
    };
    page.onConsoleMessage = function (msg) {
      console.log(msg);
    };
    page.onError = function (msg, trace) {
      console.log(msg);
      trace.forEach(function (item) {
        console.log(' ', item.file, ':', item.line);
      });
    };

    page.open(address, function (status) {
      if (status !== 'success') {
        console.log('Unable to load the address!');
        console.log("Error opening url \"" + page.reason_url + "\": " + page.reason);
        phantom.exit(1);
      }
      else {
        selectors = page.selectors;
        page.includeJs("http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js", function () {
          page.evaluate(function (selectors) {
            if (selectors.length > 0) {
              for (var i = 0; i < selectors.length; i++) {
                var selector = selectors[i].split('.');
                var sel = "";
                switch (selector[1]) {
                  case "id":
                    sel = "#" + selector[2];
                    break;
                  case "c":
                    sel = "." + selector[2];
                    break;
                }
                $("head").append("<style type='text/css'>" + sel + " { display: none !important; }</style>");
              }
            }
            ;
          }, selectors);
        });
      }
      window.setTimeout(function () {
        page.render(output, {quality: quality});
        phantom.exit();
      }, 200);
    });
  }
})();
