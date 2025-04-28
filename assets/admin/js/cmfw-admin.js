document.addEventListener("DOMContentLoaded", function () {
  const TableSizeChart = (() => {
    const table = document.getElementById("size-chart");

    if (table) {
      table.addEventListener("click", function (e) {
        const target = e.target;

        if (target && target.classList.contains("cmfw-add-column")) {
          e.preventDefault();
          addColumn(target, false);
        } else if (target && target.classList.contains("cmfw-remove-column")) {
          e.preventDefault();
          removeColumn(target);
        } else if (target && target.classList.contains("cmfw-add-row")) {
          e.preventDefault();
          addRow(target, false);
        } else if (target && target.classList.contains("cmfw-remove-row")) {
          e.preventDefault();
          removeRow(target);
        }
      });
    }

    const addRowButton = document.querySelector(".sizepsgf-add-row");

    if (addRowButton) {
      addRowButton.addEventListener("click", function (e) {
        e.preventDefault();
        const target = e.target;
        addRow(target, true);
      });
    }

    const deleteRowBtn = document.querySelector(".sizepsgf-delete-row");
    if (deleteRowBtn) {
      deleteRowBtn.addEventListener("click", function (e) {
        e.preventDefault();
        const target = e.target;
        removeRow(target);
      });
    }

    const addColumnBtn = document.querySelector(".sizepsgf-add-column");
    if (addColumnBtn) {
      addColumnBtn.addEventListener("click", function (e) {
        e.preventDefault();
        const target = e.target;
        addColumn(target, true);
      });
    }

    const deleteColumnBtn = document.querySelector(".sizepsgf-delete-column");
    if (deleteColumnBtn) {
      deleteColumnBtn.addEventListener("click", function (e) {
        e.preventDefault();
        const target = e.target;
        removeColumn(target);
      });
    }

    function addColumn(target, appendToEnd = false) {
      const table = document.getElementById("size-chart");
      if (!table) {
        console.error("Table not found!");
        return;
      }

      let clickedColumn = target.closest("td, th");

      if (!clickedColumn) {
        clickedColumn = table.querySelector("tbody td, tbody th");
        if (!clickedColumn) {
          alert("No columns found in the table!");
          return;
        }
      }

      const columnCountInput = document.querySelector(".sizepsgf-input-column");
      let columnCount = parseInt(columnCountInput.value);

      if (
        target.classList.contains("sizepsgf-add-column") &&
        (isNaN(columnCount) || columnCount <= 0)
      ) {
        alert("Please enter a valid column count!");
        return;
      }

      if (target.classList.contains("cmfw-add-column")) {
        columnCount = 1;
      }

      const cellIndex = clickedColumn.cellIndex;
      const rows = table.rows;

      for (let i = 0; i < columnCount; i++) {
        for (let j = 0; j < rows.length; j++) {
          const newCell = rows[j].insertCell(cellIndex + 1);

          if (j === 0) {
            newCell.innerHTML = `
                            <button class="btn btn-add cmfw-add-column">+</button>
                            <button class="btn btn-remove cmfw-remove-column">-</button>
                        `;
          } else {
            newCell.innerHTML = `<input type="text" placeholder="Enter value" />`;
          }

          if (appendToEnd) {
            let lastCellIndex = rows[j].cells.length - 1;
            let actionCell = rows[j].cells[lastCellIndex];

            if (actionCell) {
              rows[j].insertBefore(newCell, actionCell);
            } else {
              rows[j].appendChild(newCell);
            }
          }
        }
      }

      updateRemoveButtonsVisibility(table);
      saveSizeChartData();
    }

    function removeColumn(target) {
      const table = document.getElementById("size-chart");
      if (!table) {
        console.error("Table not found!");
        return;
      }

      const tbody = table.querySelector("tbody");
      let clickedColumn = target.closest("td, th");

      if (!clickedColumn) {
        clickedColumn = table.querySelector("thead th, tbody td");
        if (!clickedColumn) {
          alert("No columns found in the table!");
          return;
        }
      }

      const rowCountInput = document.querySelector(".sizepsgf-input-column");
      let columnCount = parseInt(rowCountInput.value);

      if (target.classList.contains("cmfw-remove-column")) {
        columnCount = 1;
      }

      if (isNaN(columnCount) || columnCount <= 0) {
        alert("Please enter a valid column count!");
        return;
      }

      const cellIndex = clickedColumn.cellIndex;
      const rows = table.rows;

      if (rows[0].cells.length <= columnCount) {
        alert("There are not enough columns to remove!");
        return;
      }

      for (let i = 0; i < columnCount; i++) {
        for (let j = 0; j < rows.length; j++) {
          if (rows[j].cells.length > 1) {
            rows[j].deleteCell(cellIndex);
          }
        }
      }

      updateRemoveButtonsVisibility(table);
      saveSizeChartData();
    }

    // Add a new row
    function addRow(target, appendToEnd = false) {
      const table = document.getElementById("size-chart");
      if (!table) {
        console.error("Table not found!");
        return;
      }

      let clickedRow = target.closest("tr");

      if (!clickedRow) {
        clickedRow = table.querySelector("tbody tr");
        if (!clickedRow) {
          alert("No rows found in the table!");
          return;
        }
      }

      const rowCountInput = document.querySelector(".sizepsgf-input");
      let rowCount = parseInt(rowCountInput.value);

      if (
        target.classList.contains("sizepsgf-add-row") &&
        (isNaN(rowCount) || rowCount <= 0)
      ) {
        alert("Please enter a valid row count!");
        return;
      }

      if (target.classList.contains("cmfw-add-row")) {
        rowCount = 1;
      }

      for (let i = 0; i < rowCount; i++) {
        const newRow = clickedRow.cloneNode(true);

        Array.from(newRow.cells).forEach((cell) => {
          const input = cell.querySelector("input");
          if (input) input.value = "";
        });

        if (appendToEnd) {
          table.querySelector("tbody").appendChild(newRow);
        } else {
          clickedRow.after(newRow);
        }
      }

      updateRemoveButtonsVisibility(table);
      saveSizeChartData();
    }

    function removeRow(target) {
      const table = document.getElementById("size-chart"); // Ensure table exists
      if (!table) {
        console.error("Table not found!");
        return;
      }

      const tbody = table.querySelector("tbody");
      const clickedRow = target.closest("tr");

      const rowCountInput = document.querySelector(".sizepsgf-input");
      let rowCount = parseInt(rowCountInput.value);

      if (target.classList.contains("cmfw-remove-row")) {
        rowCount = 1;
      }

      if (isNaN(rowCount) || rowCount <= 0) {
        alert("Please enter a valid row count!");
        return;
      }

      for (let i = 0; i < rowCount; i++) {
        if (tbody.rows.length > 1) {
          const rowToRemove = clickedRow || tbody.lastElementChild;
          tbody.removeChild(rowToRemove);
        } else {
          alert("At least one row must remain!");
          break;
        }
      }

      updateRemoveButtonsVisibility(table);
      saveSizeChartData();
    }

    function updateRemoveButtonsVisibility(table) {
      const tbody = table.querySelector("tbody");
      const columnCount = table.rows[0].cells.length;
      const rowCount = tbody.rows.length;

      table.querySelectorAll(".cmfw-remove-column").forEach((button) => {
        button.style.display = columnCount > 1 ? "inline-block" : "none";
      });

      table.querySelectorAll(".cmfw-remove-row").forEach((button) => {
        button.style.display = rowCount > 1 ? "inline-block" : "none";
      });
    }

    return {};
  })();

  // Save table data to a hidden input
  const saveSizeChartData = (() => {
    let debounceTimer;

    return function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        const table = document.getElementById("size-chart");
        const data = {
          headers: [],
          rows: [],
        };

        table.querySelectorAll("thead th").forEach((th) => {
          data.headers.push(th.innerHTML.trim());
        });

        table.querySelectorAll("tbody tr").forEach((row) => {
          const rowData = [];
          row.querySelectorAll("td input").forEach((input) => {
            rowData.push(input.value);
          });
          data.rows.push(rowData);
        });

        document.getElementById("size-chart-data").value = JSON.stringify(data);
      }, 300);
    };
  })();

  let table2 = document.getElementById("size-chart");

  if (table2) {
    document
      .getElementById("size-chart")
      .addEventListener("input", saveSizeChartData);
  }
});

// settings tab

function openTab(event, tabName) {
  const tabContents = document.querySelectorAll(
    ".sizepsgf-tab-content-settings"
  );
  tabContents.forEach((content) => {
    if (content.classList.contains("active-tab")) {
      content.classList.remove("active-tab");
    }
  });

  const tabLinks = document.querySelectorAll(".sizepsgf-tab-link");
  if (tabLinks) {
    tabLinks.forEach((link) => {
      if (link.classList.contains("active")) {
        link.classList.remove("active");
      }
    });
  }

  if (!event.currentTarget.classList.contains("active")) {
    event.currentTarget.classList.add("active");
  }

  const tabContent = document.getElementById(tabName);
  if (tabContent && !tabContent.classList.contains("active-tab")) {
    tabContent.classList.add("active-tab");
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const shortcodeFields = document.getElementsByClassName("shortcode-field");

  Array.from(shortcodeFields).forEach(function (shortcodeField) {
    shortcodeField.addEventListener("click", function () {
      this.select();
      this.setSelectionRange(0, 99999); // For mobile devices

      if (navigator.clipboard) {
        navigator.clipboard
          .writeText(this.value)
          .then(function () {
            alert("Copied!");
          })
          .catch(function (err) {
            alert("Failed to copy: " + err);
          });
      } else if (document.execCommand) {
        try {
          const successful = document.execCommand("copy");
          if (successful) {
            alert("Copied!");
          } else {
            alert("Copying failed!");
          }
        } catch (err) {
          alert("Failed to copy: " + err);
        }
      } else {
        alert("Clipboard functionality is not supported in your browser.");
      }
    });
  });
});

/*size chart add row remove row collapse js*/
document.addEventListener("DOMContentLoaded", function () {
  let toggleBtn = document.querySelector(".sizepsgf-toggle-btn");
  let content = document.querySelector(".sizepsgf-content");

  if (toggleBtn) {
    toggleBtn.addEventListener("click", function (event) {
      event.preventDefault();
      let currentDisplay = window.getComputedStyle(content).display;

      if (currentDisplay === "none") {
        content.style.display = "block";
        toggleBtn.textContent = "−";
      } else {
        content.style.display = "none";
        toggleBtn.textContent = "+";
      }
    });
  }
});

/*import json file data*/

document.addEventListener("DOMContentLoaded", function () {
  const exportBtn = document.getElementById("sizepsgf_export_data");
  const importBtnLabel = document.querySelector(".sizepsgf-import-button");
  const fileInput = document.getElementById("hiddenFileInput");

  // Export JSON Data

  if (exportBtn) {
    exportBtn.addEventListener("click", function () {
      const postID = this.getAttribute("data-sizecham_export");

      fetch(sizepsgf_localize_obj.ajax_url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=sizepsgf_export_data&post_id=${postID}&nonce=${sizepsgf_localize_obj.nonce}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            const blob = new Blob([JSON.stringify(data.data, null, 4)], {
              type: "application/json",
            });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "size_chart_data.json";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
          } else {
            alert("Export Failed!");
          }
        });
    });
  }

  if (fileInput) {
    fileInput.addEventListener("change", function (event) {
      const file = event.target.files[0];

      if (!file) return;

      const reader = new FileReader();
      reader.onload = function (e) {
        const jsonData = e.target.result;
        const postID = importBtnLabel.getAttribute("data-sizecham_import");

        fetch(sizepsgf_localize_obj.ajax_url, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `action=sizepsgf_import_data&post_id=${postID}&data=${encodeURIComponent(
            jsonData
          )}&nonce=${sizepsgf_localize_obj.nonce}`,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              alert("Import Successful! Reloading page...");
              location.reload();
            } else {
              alert("Import Failed!");
            }
          });
      };
      reader.readAsText(file);
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  function togglePopupIcon() {
    let positionSelect = document.querySelector("#sizepsgf_chart_position");
    let popupIconField = document.querySelector(".sizepsgf-popup-setting-wrap");

    if (!positionSelect || !popupIconField) {
      return;
    }

    popupIconField.style.display =
      positionSelect.value === "modal" ? "block" : "none";
  }

  let positionSelect = document.querySelector("#sizepsgf_chart_position");
  if (positionSelect) {
    togglePopupIcon(); // Call initially
    positionSelect.addEventListener("change", togglePopupIcon);
  }
});
