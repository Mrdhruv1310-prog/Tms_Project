import "flowbite";
import { Tooltip } from "flowbite";
import AirDatepicker from "air-datepicker";
import "air-datepicker/air-datepicker.css";
import localeEn from "air-datepicker/locale/en";
import {
  computePosition,
  autoUpdate,
  flip,
  shift,
  offset,
} from "@floating-ui/dom";

// Helper: Check mobile device efficiently
const isMobileDevice = () => window.innerWidth <= 768;

// Safe Tooltip Initialization
function initTooltips() {
  const triggerElements = document.querySelectorAll(".tooltip-button");
  const tooltipContents = document.querySelectorAll(".tooltip-content");

  if (!triggerElements.length) return;

  const triggerType = isMobileDevice() ? "click" : "hover";

  triggerElements.forEach((triggerEl, index) => {
    const tooltipContent = tooltipContents[index];
    if (!tooltipContent) return;

    // Prevent form submission if it's inside a form/button
    triggerEl.addEventListener("click", (event) => {
      if (
        triggerEl.tagName === "BUTTON" ||
        triggerEl.getAttribute("type") === "submit"
      ) {
        event.preventDefault();
      }
    });

    triggerEl.setAttribute("data-tooltip-trigger", triggerType);

    new Tooltip(
      tooltipContent,
      triggerEl,
      {
        placement: "bottom",
        triggerType: triggerType,
      },
      {
        id: `tooltipContent-${index}`,
        override: true,
      },
    );
  });
}

// Global Datepicker Management
let dateTimePickerInstance = null;
let datePickerInstance = null;

window.initializeDateTimepicker = function (
  selector,
  containerSelector,
  dueDateTime = null,
) {
  const targetInput = document.querySelector(selector);
  if (!targetInput) return;

  const today = new Date();
  const todayButton = {
    content: "Today",
    className: "today-custom-button",
    onClick: (dp) => {
      dp.selectDate(today);
      dp.setViewDate(today);
    },
  };

  if (dateTimePickerInstance) {
    dateTimePickerInstance.destroy();
  }

  dateTimePickerInstance = new AirDatepicker(selector, {
    startDate: today,
    dateFormat: "dd/MM/yyyy",
    timeFormat: "HH:mm",
    minDate: today,
    container: containerSelector,
    visible: false,
    locale: localeEn,
    timepicker: true,
    toggleSelected: false,
    buttons: [todayButton, "clear"],
    onSelect({ formattedDate }) {
      if (formattedDate) {
        targetInput.value = formattedDate;
        targetInput.dispatchEvent(new Event("input", { bubbles: true }));
        targetInput.dispatchEvent(new Event("change", { bubbles: true }));
      } else {
        targetInput.value = "";
        targetInput.dispatchEvent(new Event("input", { bubbles: true }));
      }
    },
    position({ $datepicker, $target, done }) {
      const updatePosition = () => {
        computePosition($target, $datepicker, {
          placement: "top",
          middleware: [flip(), offset(20), shift({ padding: { top: 64 } })],
        }).then(({ x, y }) => {
          Object.assign($datepicker.style, {
            left: `${x}px`,
            top: `${y}px`,
          });
        });
      };
      const cleanup = autoUpdate($target, $datepicker, updatePosition);
      updatePosition();

      return function completeHide() {
        cleanup();
        done();
      };
    },
  });

  if (dueDateTime) {
    const [dateStr, timeStr] = dueDateTime.split(" ");
    if (dateStr && timeStr) {
      const [day, month, year] = dateStr.split("/");
      const [hours, minutes] = timeStr.split(":");
      const selectedDate = new Date(year, month - 1, day, hours, minutes);
      dateTimePickerInstance.selectDate(selectedDate);
    }
  }
};

window.initializeDatepicker = function (
  selector,
  containerSelector,
  dueDate = null,
) {
  const targetInput = document.querySelector(selector);
  if (!targetInput) return;

  const today = new Date();
  const todayButton = {
    content: "Today",
    className: "today-custom-button",
    onClick: (dp) => {
      dp.selectDate(today);
      dp.setViewDate(today);
    },
  };

  if (datePickerInstance) {
    datePickerInstance.destroy();
  }

  datePickerInstance = new AirDatepicker(selector, {
    dateFormat: "dd/MM/yyyy",
    minDate: today,
    container: containerSelector,
    visible: false,
    locale: localeEn,
    timepicker: false,
    toggleSelected: false,
    buttons: [todayButton, "clear"],
    onSelect({ formattedDate }) {
      if (formattedDate) {
        targetInput.value = formattedDate;
        targetInput.dispatchEvent(new Event("input", { bubbles: true }));
        targetInput.dispatchEvent(new Event("change", { bubbles: true }));
      } else {
        targetInput.value = "";
        targetInput.dispatchEvent(new Event("input", { bubbles: true }));
      }
    },
    position({ $datepicker, $target, done }) {
      const updatePosition = () => {
        computePosition($target, $datepicker, {
          placement: "top",
          middleware: [flip(), offset(20), shift({ padding: { top: 64 } })],
        }).then(({ x, y }) => {
          Object.assign($datepicker.style, {
            left: `${x}px`,
            top: `${y}px`,
          });
        });
      };
      const cleanup = autoUpdate($target, $datepicker, updatePosition);
      updatePosition();

      return function completeHide() {
        cleanup();
        done();
      };
    },
  });

  if (dueDate) {
    const [dateStr] = dueDate.split(" ");
    if (dateStr) {
      const [day, month, year] = dateStr.split("/");
      const selectedDate = new Date(year, month - 1, day);
      datePickerInstance.selectDate(selectedDate);
    }
  }
};

window.destroyDatepicker = function () {
  if (dateTimePickerInstance) {
    dateTimePickerInstance.destroy();
    dateTimePickerInstance = null;
  }
  if (datePickerInstance) {
    datePickerInstance.destroy();
    datePickerInstance = null;
  }
};

// Dial Components Initialization
function initDials() {
  const setupDial = (btnId, parentId, contentId) => {
    const triggerEl = document.getElementById(btnId);
    const parentEl = document.getElementById(parentId);
    const targetEl = document.getElementById(contentId);

    if (triggerEl && parentEl && targetEl && typeof Dial !== "undefined") {
      const dial = new Dial(parentEl, triggerEl, targetEl);
      triggerEl.addEventListener("click", (e) => {
        e.stopPropagation();
        dial.toggle();
      });

      document.addEventListener("click", (e) => {
        if (!parentEl.contains(e.target) && dial._visible) {
          dial.toggle();
        }
      });
    }
  };

  setupDial(
    "bottomnavaddmenuBtn",
    "bottomnavaddmenuParent",
    "bottomnavaddmenuContent",
  );
  setupDial(
    "bottomnavmoremenuBtn",
    "bottomnavmoremenuParent",
    "bottomnavmoremenuContent",
  );
}

// PWA & Service Worker
function initPWA() {
  if ("serviceWorker" in navigator) {
    navigator.serviceWorker.register("/sw.js").catch(() => {});
  }

  let deferredPrompt = null;
  const installBtn = document.querySelector("#install");

  const getPWADisplayMode = () => {
    if (document.referrer.startsWith("android-app://")) return "twa";
    if (window.matchMedia("(display-mode: browser)").matches) return "browser";
    if (window.matchMedia("(display-mode: standalone)").matches)
      return "standalone";
    if (window.matchMedia("(display-mode: minimal-ui)").matches)
      return "minimal-ui";
    if (window.matchMedia("(display-mode: fullscreen)").matches)
      return "fullscreen";
    return "unknown";
  };

  if (installBtn) {
    const displayMode = getPWADisplayMode();
    if (displayMode === "browser" || displayMode === "unknown") {
      installBtn.style.display = "block";
    } else {
      installBtn.style.display = "none";
    }

    window.addEventListener("beforeinstallprompt", (e) => {
      e.preventDefault();
      deferredPrompt = e;
      installBtn.style.display = "block";
    });

    installBtn.addEventListener("click", async () => {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        deferredPrompt = null;
        if (outcome === "accepted") {
          installBtn.style.display = "none";
        }
      }
    });
  }
}

// Bootstrapper
document.addEventListener("DOMContentLoaded", () => {
  if (typeof initFlowbite === "function") {
    initFlowbite();
  }
  initTooltips();
  initDials();
  initPWA();
});

// Livewire Navigation Re-initialization
document.addEventListener("livewire:navigated", () => {
  if (typeof initFlowbite === "function") {
    initFlowbite();
  }
  initTooltips();
  initDials();
});
