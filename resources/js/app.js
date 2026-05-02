import 'flowbite';
import { Tooltip } from 'flowbite';
import AirDatepicker from 'air-datepicker';
import 'air-datepicker/air-datepicker.css';
import localeEn from 'air-datepicker/locale/en';

import { computePosition, autoUpdate, flip, shift, offset } from '@floating-ui/dom';

// Function to detect if the device is mobile
    function isMobileDevice() {
        return window.innerWidth <= 768;
    }
    // Set tooltip trigger based on device type
    const tooltipButton = document.querySelector('.tooltip-button');
    tooltipButton.addEventListener('click', function(event) {
        console.log('Tooltip button clicked');
        event.preventDefault();  // Prevent form submission
    });
    if (isMobileDevice()) {
        console.log(window.innerWidth);
        tooltipButton.setAttribute('data-tooltip-trigger', 'click');
    } else {
        tooltipButton.setAttribute('data-tooltip-trigger', 'hover');
    }
    // Detect device and set appropriate trigger type
    const triggerType = isMobileDevice() ? 'click' : 'hover';

    // Select all trigger elements and tooltip content elements
    const triggerElements = document.querySelectorAll('.tooltip-button');
    const tooltipContents = document.querySelectorAll('.tooltip-content');
    // Loop through all the tooltip elements and initialize a Tooltip instance for each
    triggerElements.forEach((triggerEl, index) => {
        const tooltipContent = tooltipContents[index];

        // options with default values
        const options = {
            placement: 'bottom',
            triggerType: triggerType,
            onHide: () => {
                console.log('tooltip is hidden');
            },
            onShow: () => {
                console.log('tooltip is shown');
            },
            onToggle: () => {
                console.log('tooltip is toggled');
            },
        };

        // instance options with default values
        const instanceOptions = {
            id: `tooltipContent-${index}`,
            override: true
        };

        // Initialize Tooltip object
        const tooltip = new Tooltip(tooltipContent, triggerEl, options, instanceOptions);
    });

    let dateTimePickerInstance = null;
    window.initializeDateTimepicker = function initializeDateTimepicker(selector, containerSelector, dueDateTime = null) {
      let today = new Date();
      let button = {
        content: 'Today',
        className: 'today-custom-button',
        onClick: (dp) => {
            dp.selectDate(today);
            dp.setViewDate(today);
        }
      }
      dateTimePickerInstance = new AirDatepicker(selector, {
          startDate: today,
          dateFormat: 'dd/MM/yyyy',
          timeFormat: 'HH:mm',
          minDate : today,
          container: containerSelector,
          visible: false,
          locale: localeEn,
          timepicker: true,
          toggleSelected: false,
          buttons: [button, 'clear'],
          // autoClose: true,
          onSelect({ formattedDate }) {
            // Check if formattedDate is defined and not empty
            if (formattedDate) {
                // Update input field with the formatted date
                document.querySelector(selector).value = formattedDate;
                document.querySelector(selector).dispatchEvent(new Event('input'));
                document.querySelector(selector).dispatchEvent(new Event('change')); // Trigger change event
            } else {
                // Handle the case where no date is selected (date is deselected)
                console.log("Date deselected or invalid date");
                // Optionally clear the input field or handle it accordingly
                document.querySelector(selector).value = ''; // Clear the input
                document.querySelector(selector).dispatchEvent(new Event('input'));
            }
        },        
          position({ $datepicker, $target, $pointer, done }) {

            const updatePosition = () => {
              computePosition($target, $datepicker, {
                placement: 'top',
                middleware: [
                  flip(),
                  offset(20),
                  shift({
                  padding: {
                  top: 64
                  }
                  }),
                ],
              }).then(({ x, y }) => {
                Object.assign($datepicker.style, {
                left: `${x}px`,
                top: `${y}px`,
                });
              });
            };
            // console.log($target);
            // console.log($datepicker);
            // console.log($pointer);
            const cleanup = autoUpdate($target, $datepicker, updatePosition);
            updatePosition();

            return function completeHide() {
              cleanup();
              done();
            };
          }
          
      });
      if(dueDateTime != null){
        let dbDate = dueDateTime;
        // Convert the date string into a JavaScript Date object
        let dateParts = dbDate.split(' ');
        let date = dateParts[0].split('/');
        let time = dateParts[1].split(':');
  
        // Create the new Date object with the date and time from the database
        let selectedDate = new Date(date[2], date[1] - 1, date[0], time[0], time[1]);
  
        // Assuming `datePicker` is your Air Datepicker instance
        dateTimePickerInstance.selectDate(selectedDate);
        console.log('date is selected');
      }
      // else{
      //   dateTimePickerInstance.selectDate(today);
      // }
    }
    // Function to destroy the datepicker instance manually
    window.destroyDatepicker = function() {
      if (dateTimePickerInstance) {
          dateTimePickerInstance.destroy();
          dateTimePickerInstance = null;
      }
    };
  
    let datePickerInstance = null;

    window.initializeDatepicker = function initializeDatepicker(selector, containerSelector, dueDate = null) {
      let today = new Date();
      let button = {
        content: 'Today',
        className: 'today-custom-button',
        onClick: (dp) => {
            dp.selectDate(today);
            dp.setViewDate(today);
        }
      }
      datePickerInstance = new AirDatepicker(selector, {
          // startDate: today,
          dateFormat: 'dd/MM/yyyy',
          minDate : today,
          container: containerSelector,
          visible: false,
          locale: localeEn,
          timepicker: false,
          toggleSelected: false,
          buttons: [button, 'clear'],
          // autoClose: true,
          onSelect({ formattedDate }) {
            // Check if formattedDate is defined and not empty
            if (formattedDate) {
                // Update input field with the formatted date
                document.querySelector(selector).value = formattedDate;
                document.querySelector(selector).dispatchEvent(new Event('input'));
                document.querySelector(selector).dispatchEvent(new Event('change')); // Trigger change event
            } else {
                // Handle the case where no date is selected (date is deselected)
                console.log("Date deselected or invalid date");
                // Optionally clear the input field or handle it accordingly
                document.querySelector(selector).value = ''; // Clear the input
                document.querySelector(selector).dispatchEvent(new Event('input'));
            }
        },        
          position({ $datepicker, $target, $pointer, done }) {

            const updatePosition = () => {
              computePosition($target, $datepicker, {
                placement: 'top',
                middleware: [
                  flip(),
                  offset(20),
                  shift({
                  padding: {
                  top: 64
                  }
                  }),
                ],
              }).then(({ x, y }) => {
                Object.assign($datepicker.style, {
                left: `${x}px`,
                top: `${y}px`,
                });
              });
            };
            // console.log($target);
            // console.log($datepicker);
            // console.log($pointer);
            const cleanup = autoUpdate($target, $datepicker, updatePosition);
            updatePosition();

            return function completeHide() {
              cleanup();
              done();
            };
          }
          
      });

      console.log(dueDate);
      if(dueDate != null){
        let dbDate = dueDate;
        // Convert the date string into a JavaScript Date object
        let dateParts = dbDate.split(' ');
        console.log(dateParts);
        let date = dateParts[0].split('/');
  
        // Create the new Date object with the date and time from the database
        let selectedDate = new Date(date[2], date[1] - 1, date[0]);
  
        // Assuming `datePicker` is your Air Datepicker instance
        datePickerInstance.selectDate(selectedDate);
        console.log('date is selected');
      }
      // else{
      //   dateTimePickerInstance.selectDate(today);
      // }
    }

    // Function to destroy the datepicker instance manually
    window.destroyDatepicker = function() {
      if (datePickerInstance) {
          datePickerInstance.destroy();
          datePickerInstance = null;
      }
    };
    // Wait for the DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize Flowbite
      initFlowbite();

      console.log('Flowbite initialized on DOMContentLoaded');

      // Bottom Nav Add Menu
      const bottomNavAdd$triggerEl = document.getElementById('bottomnavaddmenuBtn');
      const bottomNavAdd$parentEl = document.getElementById('bottomnavaddmenuParent');
      const bottomNavAdd$targetEl = document.getElementById('bottomnavaddmenuContent');
      
      // Bottom Nav More Menu
      const bottomNavMoreMenu$triggerEl = document.getElementById('bottomnavmoremenuBtn');
      const bottomNavMoreMenu$parentEl = document.getElementById('bottomnavmoremenuParent');
      const bottomNavMoreMenu$targetEl = document.getElementById('bottomnavmoremenuContent');
      
      // Initialize the Dial components
      if (bottomNavAdd$triggerEl && bottomNavAdd$parentEl && bottomNavAdd$targetEl) {
          const bottomNavAdddial = new Dial(bottomNavAdd$parentEl, bottomNavAdd$triggerEl, bottomNavAdd$targetEl);
          bottomNavAdd$triggerEl.addEventListener('click', (e) => {
              e.stopPropagation();
              bottomNavAdddial.toggle();
          });

          document.addEventListener('click', (e) => {
              if (!bottomNavAdd$parentEl.contains(e.target) && bottomNavAdddial._visible) {
                  bottomNavAdddial.toggle();
              }
          });
      }

      if (bottomNavMoreMenu$triggerEl && bottomNavMoreMenu$parentEl && bottomNavMoreMenu$targetEl) {
          const bottomNavMoreMenudial = new Dial(bottomNavMoreMenu$parentEl, bottomNavMoreMenu$triggerEl, bottomNavMoreMenu$targetEl);
          bottomNavMoreMenu$triggerEl.addEventListener('click', (e) => {
              e.stopPropagation();
              bottomNavMoreMenudial.toggle();
          });

          document.addEventListener('click', (e) => {
              if (!bottomNavMoreMenu$parentEl.contains(e.target) && bottomNavMoreMenudial._visible) {
                  bottomNavMoreMenudial.toggle();
              }
          });
      }
      //call datepicker destroyer function
      // destroyDatepicker();
    });

    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/sw.js').then((registration) => {
        console.log('Service Worker registered:', registration);
      }).catch((error) => {
        console.error('Service Worker registration failed:', error);
      });
    }

    function getPWADisplayMode() {
      if (document.referrer.startsWith('android-app://'))
        return 'twa';
      if (window.matchMedia('(display-mode: browser)').matches)
        return 'browser';
      if (window.matchMedia('(display-mode: standalone)').matches)
        return 'standalone';
      if (window.matchMedia('(display-mode: minimal-ui)').matches)
        return 'minimal-ui';
      if (window.matchMedia('(display-mode: fullscreen)').matches)
        return 'fullscreen';
      if (window.matchMedia('(display-mode: window-controls-overlay)').matches)
        return 'window-controls-overlay';
    
      return 'unknown';
    }

    window.addEventListener("DOMContentLoaded", async event => {
      if ('BeforeInstallPromptEvent' in window) {
        showResult("⏳ BeforeInstallPromptEvent supported but not fired yet");
      } else {
        showResult("❌ BeforeInstallPromptEvent NOT supported");    
      }
      let displayMode = getPWADisplayMode();
      if (displayMode === 'browser' || displayMode === 'unknown') {
        showResult("❌ PWA not installed yet, display mode: " + displayMode);      
        document.querySelector("#install").style.display="block";
      }
      else{
        showResult("✅ PWA already installed, display mode: " + displayMode);
        document.querySelector("#install").style.display="none";
      }
      document.querySelector("#install").addEventListener("click", installApp);
    });
    
    let deferredPrompt;
    
    window.addEventListener('beforeinstallprompt', (e) => {
      // Prevents the default mini-infobar or install dialog from appearing on mobile
      e.preventDefault();
      // Save the event because you’ll need to trigger it later.
      deferredPrompt = e;
      // Show your customized install prompt for your PWA
      document.querySelector("#install").style.display="block";  
      showResult("✅ BeforeInstallPromptEvent fired", true);
      
    });
    
    window.addEventListener('appinstalled', (e) => {
      showResult("✅ AppInstalled fired", true);
    });
    
    async function installApp() {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        showResult("🆗 Installation Dialog opened");
        // Find out whether the user confirmed the installation or not
        const { outcome } = await deferredPrompt.userChoice;
        // The deferredPrompt can only be used once.
        deferredPrompt = null;
        // Act on the user's choice
        if (outcome === 'accepted') {
          showResult('😀 User accepted the install prompt.', true);
        } else if (outcome === 'dismissed') {
          showResult('😟 User dismissed the install prompt');
        }
        // We hide the install button
        document.querySelector("#install").style.display="none";
    
      }
    }
    
    function showResult(text, append=false) {
      if (append) {
        console.log(text);
          // document.querySelector("output").innerHTML += "<br>" + text;
      } else {
        console.log(text);
        //  document.querySelector("output").innerHTML = text;    
      }
    }

// Livewire.on('componentMounted', (component) => {
// console.log(`${component} mounted`);
// });

// Livewire.on('componentUpdated', (component, property) => {
// console.log(`${component} updated: ${property}`);
// });

// Livewire.on('componentDestroyed', (component) => {
// console.log(`${component} destroyed`);
// });