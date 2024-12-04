<x-app-layout>
    @section('header')
        Auftrag anlegen
    @endsection

@section('main')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form id="orderForm" method="POST" action="{{ route('orders.store') }}">
                            @csrf
                            <!-- Search Field -->
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

                            <!-- Loading Spinner -->
                            <div id="searchLoading" class="text-center d-none">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>

                            <!-- Search Results -->
                            <div id="searchResults" class="list-group d-none mb-3">
                                <!-- Results will be inserted here -->
                            </div>

                            <!-- Customer Form Fields -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="customer_number" class="form-label">Kundennummer</label>
                                    <input type="text" class="form-control" id="customer_number" name="customer_number" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label for="companyname" class="form-label">Firma</label>
                                    <input type="text" class="form-control" id="companyname" name="companyname" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label for="vorname" class="form-label">Vorname</label>
                                    <input type="text" class="form-control" id="vorname" name="vorname" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label for="nachname" class="form-label">Nachname</label>
                                    <input type="text" class="form-control" id="nachname" name="nachname" readonly>
                                </div>

                                <input type="hidden" id="customer_id" name="customer_id">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @include('orders.availability-calender')

        <div class="row justify-content-center mt-3">
            <div class="col-md-8">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button form="orderForm" class="btn btn-primary me-md-2" type="submit">Speichern</button>
                    <a href="{{ route('orders') }}" class="btn btn-secondary">Abbrechen</a>
                </div>
            </div>
        </div>
    </div>

<!-- Result Template -->
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

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout = null;
    const searchInput = document.getElementById('customerSearch');
    const searchResults = document.getElementById('searchResults');
    const loadingSpinner = document.getElementById('searchLoading');
    const resultTemplate = document.getElementById('customer-result-template');

    // Search input handler
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
        // Fill all form fields with customer data
        document.getElementById('customer_id').value = customer.id;
        document.getElementById('customer_number').value = customer.customer_number;
        document.getElementById('companyname').value = customer.companyname;
        document.getElementById('vorname').value = customer.vorname;
        document.getElementById('nachname').value = customer.nachname;
        
        // Clear search and hide results
        searchInput.value = '';
        searchResults.classList.add('d-none');
        fetchAvailabilities()
    }

    // Close results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('d-none');
        }
    });

    async function fetchAvailabilities() {
        try {
            const response = await fetch(`/orders/availabilities`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            console.log("response", response);
            const availabilities = await response.json();
            handleAvailabilities(availabilities);
        } catch (error) {
            console.error('Failed to fetch availabilities:', error);
        }
    }

    function handleAvailabilities(availabilities) {
        // Here you can handle the availabilities data
        // For example, update a select field or display them in a table
        console.log('Received availabilities:', availabilities);
        
        // Example: Update a select field with the availabilities
        const availabilitySelect = document.getElementById('availability_slot');
        if (availabilitySelect) {
            availabilitySelect.innerHTML = '';
            
            // Add default option
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Bitte wählen Sie einen Termin';
            availabilitySelect.appendChild(defaultOption);
            
            // Add availabilities as options
            availabilities.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot.id;
                option.textContent = `${slot.date} ${slot.time} - ${slot.duration} Min`;
                availabilitySelect.appendChild(option);
            });
        }
    }
});
</script>
@endpush
</x-app-layout>