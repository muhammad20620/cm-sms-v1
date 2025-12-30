//Bootstrap Tooltip initialize
var tooltipTriggerList = [].slice.call(
  document.querySelectorAll('[data-bs-toggle="tooltip"]')
);
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl);
});

/**
 * Client-side file validation (prevents selecting files that backend will reject).
 *
 * Rules:
 * - Uses the input's `accept` attribute to validate MIME/extension
 * - Optional `data-max-kb="4096"` to validate size
 * - Shows toastr error (fallback to alert) and clears the input on failure
 */
(function () {
  "use strict";
  
  
  function showError(message) {
    if (typeof toastr !== "undefined" && toastr && typeof toastr.error === "function") {
      toastr.error(message);
      return;
    }
    alert(message);
  }

  function parseAccept(accept) {
    return (accept || "")
      .split(",")
      .map(function (s) { return s.trim().toLowerCase(); })
      .filter(Boolean);
  }

  function getExt(filename) {
    var parts = (filename || "").split(".");
    return parts.length > 1 ? parts.pop().toLowerCase() : "";
  }

  function acceptMatches(file, acceptList) {
    if (!acceptList.length) return true;

    var type = (file.type || "").toLowerCase();
    var ext = getExt(file.name);

    return acceptList.some(function (a) {
      if (a === "*/*") return true;
      if (a.endsWith("/*")) return type.startsWith(a.slice(0, -1));
      if (a.startsWith(".")) return ext && ("." + ext) === a;
      return type && type === a;
    });
  }

  function validateFileInput(input) {
    if (!input || !input.files || !input.files.length) return true;

    var file = input.files[0];
    var acceptList = parseAccept(input.getAttribute("accept"));

    if (!acceptMatches(file, acceptList)) {
      showError("Invalid file type. Please upload a supported image format.");
      input.value = "";
      return false;
    }

    var maxKb = parseInt(input.getAttribute("data-max-kb") || "", 10);
    if (!isNaN(maxKb) && maxKb > 0 && file.size > maxKb * 1024) {
      showError("File too large. Max size is " + maxKb + "KB.");
      input.value = "";
      return false;
    }

    return true;
  }

  document.addEventListener("change", function (e) {
    var el = e.target;
    if (el && el.matches && el.matches('input[type="file"][data-validate-file="1"]')) {
      validateFileInput(el);
    }
  });

  document.addEventListener("submit", function (e) {
    var form = e.target;
    if (!form || !form.querySelectorAll) return;

    var inputs = form.querySelectorAll('input[type="file"][data-validate-file="1"]');
    for (var i = 0; i < inputs.length; i++) {
      if (!validateFileInput(inputs[i])) {
        e.preventDefault();
        e.stopPropagation();
        return false;
      }
    }
  }, true);
})();
