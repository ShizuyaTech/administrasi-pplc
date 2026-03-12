/**
 * Employee Autocomplete Component
 * Provides type-ahead search functionality for employee name inputs
 */

function initEmployeeAutocomplete(inputElement, options = {}) {
    const {
        sectionIdGetter = () => null,
        onSelect = null,
        minChars = 2,
        debounceDelay = 300
    } = options;

    let debounceTimer = null;
    let selectedIndex = -1;
    let currentResults = [];

    // Create dropdown container
    const dropdown = document.createElement('div');
    dropdown.className = 'employee-autocomplete-dropdown';
    dropdown.style.cssText = `
        position: absolute;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        width: ${inputElement.offsetWidth}px;
    `;

    // Insert dropdown after input
    inputElement.parentElement.style.position = 'relative';
    inputElement.parentElement.appendChild(dropdown);

    // Input event listener with debounce
    inputElement.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        const query = e.target.value.trim();

        if (query.length >= minChars) {
            debounceTimer = setTimeout(() => {
                fetchEmployees(query);
            }, debounceDelay);
        } else {
            hideDropdown();
        }
    });

    // Fetch employees from API
    async function fetchEmployees(query) {
        try {
            const sectionId = sectionIdGetter();
            let url = `/employees/search?q=${encodeURIComponent(query)}`;
            if (sectionId) {
                url += `&section_id=${sectionId}`;
            }

            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const employees = await response.json();
            currentResults = employees;
            displayResults(employees);
        } catch (error) {
            console.error('Error fetching employees:', error);
            hideDropdown();
        }
    }

    // Display search results
    function displayResults(employees) {
        if (employees.length === 0) {
            dropdown.innerHTML = '<div style="padding: 0.75rem; color: #6b7280;">Tidak ada pegawai ditemukan</div>';
            showDropdown();
            return;
        }

        dropdown.innerHTML = employees.map((emp, index) => `
            <div class="autocomplete-item" 
                 data-index="${index}" 
                 data-name="${emp.name}"
                 style="padding: 0.75rem; cursor: pointer; border-bottom: 1px solid #f3f4f6; ${index === selectedIndex ? 'background-color: #f3f4f6;' : ''}"
                 onmouseover="this.style.backgroundColor='#f3f4f6'"
                 onmouseout="this.style.backgroundColor='${index === selectedIndex ? '#f3f4f6' : 'white'}'">
                <div style="font-weight: 600; color: #1f2937;">${emp.name}</div>
                <div style="font-size: 0.875rem; color: #6b7280;">NRP: ${emp.nrp} | ${emp.position}</div>
            </div>
        `).join('');

        selectedIndex = -1;
        showDropdown();
    }

    // Show dropdown
    function showDropdown() {
        dropdown.style.display = 'block';
    }

    // Hide dropdown
    function hideDropdown() {
        dropdown.style.display = 'none';
        selectedIndex = -1;
        currentResults = [];
    }

    // Handle keyboard navigation
    inputElement.addEventListener('keydown', (e) => {
        if (dropdown.style.display === 'none') return;

        const items = dropdown.querySelectorAll('.autocomplete-item');
        
        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                updateSelection(items);
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                selectedIndex = Math.max(selectedIndex - 1, -1);
                updateSelection(items);
                break;
                
            case 'Enter':
                e.preventDefault();
                if (selectedIndex >= 0 && currentResults[selectedIndex]) {
                    selectEmployee(currentResults[selectedIndex]);
                }
                break;
                
            case 'Escape':
                e.preventDefault();
                hideDropdown();
                break;
        }
    });

    // Update visual selection
    function updateSelection(items) {
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.style.backgroundColor = '#f3f4f6';
                item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            } else {
                item.style.backgroundColor = 'white';
            }
        });
    }

    // Handle click on result
    dropdown.addEventListener('click', (e) => {
        const item = e.target.closest('.autocomplete-item');
        if (item) {
            const index = parseInt(item.dataset.index);
            const employee = currentResults[index];
            if (employee) {
                selectEmployee(employee);
            }
        }
    });

    // Select employee and fill input
    function selectEmployee(employee) {
        inputElement.value = employee.name;
        hideDropdown();
        
        // Trigger input event for frameworks like Alpine.js
        const event = new Event('input', { bubbles: true });
        inputElement.dispatchEvent(event);

        // Call custom callback if provided
        if (onSelect && typeof onSelect === 'function') {
            onSelect(employee);
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!inputElement.contains(e.target) && !dropdown.contains(e.target)) {
            hideDropdown();
        }
    });

    // Update dropdown width on window resize
    window.addEventListener('resize', () => {
        dropdown.style.width = `${inputElement.offsetWidth}px`;
    });

    // Return cleanup function
    return function cleanup() {
        dropdown.remove();
    };
}

// Export for use in modules or make globally available
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initEmployeeAutocomplete };
} else {
    window.initEmployeeAutocomplete = initEmployeeAutocomplete;
}
