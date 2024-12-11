<div class="position-relative">
    <div class="input-group mb-3">
        <span class="input-group-text">
            <i class="bi bi-search"></i>
        </span>
        <input 
            type="text" 
            class="form-control" 
            id="customerSearch" 
            placeholder="Kunde suchen (Name, Firma oder Kundennummer)" 
            autocomplete="off"
        >
    </div>

    <div id="searchLoading" class="text-center d-none position-absolute w-100" style="top: 100%; z-index: 1000;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div id="searchResults" class="list-group d-none position-absolute w-100" 
         style="top: 100%; z-index: 1000; max-height: 300px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    </div>
</div>

<template id="customer-result-template">
    <a href="#" class="list-group-item list-group-item-action">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-1 customer-name"></h6>
                <p class="mb-1 company-name text-muted"></p>
            </div>
            <small class="customer-number text-muted"></small>
        </div>
    </a>
</template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout = null;
    const searchInput = document.getElementById('customerSearch');
    const searchResults = document.getElementById('searchResults');
    const loadingSpinner = document.getElementById('searchLoading');
    const resultTemplate = document.getElementById('customer-result-template');

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.trim();
        
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        if (searchTerm.length < 2) {
            searchResults.classList.add('d-none');
            return;
        }
        
        searchTimeout = setTimeout(() => {
            performSearch(searchTerm);
        }, 300);
    });

    async function performSearch(searchTerm) {
        loadingSpinner.classList.remove('d-none');
        searchResults.classList.add('d-none');
        
        try {
            const response = await fetch(`/orders/search?term=${encodeURIComponent(searchTerm)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const customers = await response.json();
            displayResults(customers);
        } catch (error) {
            console.error('Search failed:', error);
        } finally {
            loadingSpinner.classList.add('d-none');
        }
    }

    function displayResults(customers) {
        searchResults.innerHTML = '';
        
        if (customers.length > 0) {
            customers.forEach(customer => {
                const resultElement = resultTemplate.content.cloneNode(true);
                
                resultElement.querySelector('.customer-name').textContent = 
                    `${customer.nachname}, ${customer.vorname}`;
                resultElement.querySelector('.company-name').textContent = 
                    customer.companyname;
                resultElement.querySelector('.customer-number').textContent = 
                    customer.customer_number;
                
                const listItem = resultElement.querySelector('.list-group-item');
                listItem.addEventListener('click', (e) => {
                    e.preventDefault();
                    selectCustomer(customer);
                });
                
                searchResults.appendChild(resultElement);
            });
            
            searchResults.classList.remove('d-none');
        }
    }

    function selectCustomer(customer) {
        document.getElementById('customer_id').value = customer.id;
        document.getElementById('customer_number').value = customer.customer_number;
        document.getElementById('companyname').value = customer.companyname;
        document.getElementById('vorname').value = customer.vorname;
        document.getElementById('nachname').value = customer.nachname;
        document.getElementById('address').value = customer.address.street+' '+customer.address.house_number+' '+customer.address.zip_code+' '+customer.address.city;
        console.log(customer)
        searchInput.value = '';
        searchResults.classList.add('d-none');
        
    }

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('d-none');
        }
    });
});
</script>
@endpush