import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = [
      'searchInput',
      'tableBody',
      'familySelector',
      'brandSelector',
      'currentPageIndicator',
      'totalPageIndicator',
      'totalElementsIndicator',
      'previousBtn',
      'nextBtn'
    ]

    static values = {
        payload: Array,
        filteredpayload: Array,
        page: {type: Number, default: 1},
        elementsPerPage:  {type: Number, default: 25},
        selectedFamille: String,
        selectedMarque: String
    }

    connect() {
        // Call methods to get unique families and brands
        const uniqueFamilies = this.getUniqueFamilies();
        const uniqueBrands = this.getUniqueBrands();

        // Fill the options of the family and brand filters
        this.fillOptions(this.familySelectorTarget, uniqueFamilies);
        this.fillOptions(this.brandSelectorTarget, uniqueBrands);
    }

    // Trigger each time page value changed
    pageValueChanged() {
      let pagination = this.paginator()
      this.pageValue == 1 
        ? this.previousBtnTarget.style.display = 'none' 
        : this.previousBtnTarget.style.display = 'flex' 
      this.pageValue == pagination.total_pages 
        ? this.nextBtnTarget.style.display = 'none'
        : this.nextBtnTarget.style.display = 'flex'

      this.currentPageIndicatorTarget.innerHTML = `${this.pageValue}`
    }

    // Search a materiel in the payload 
    search(e, inputValue) {
      const value = e ? e.target.value : inputValue
      this.filteredpayloadValue = this.payloadValue;

      // Apply famille filter
      if (this.selectedFamille) {
        this.filteredpayloadValue = this.filteredpayloadValue.filter(materiel => materiel.type.famille === this.selectedFamille);
      }

      // Apply marque filter
      if (this.selectedMarque) {
        this.filteredpayloadValue = this.filteredpayloadValue.filter(materiel => materiel.marque === this.selectedMarque);
      }

      if (value.length >= 3) {
        this.filteredpayloadValue = this.filteredpayloadValue.filter(materiel => 
            (materiel.nom && materiel.nom.toLowerCase().includes(value.toLowerCase()) )
            || (materiel.nom_court && materiel.nom_court.toLowerCase().includes(value.toLowerCase()) )
            || (materiel.commentaire && materiel.commentaire.toLowerCase().includes(value.toLowerCase()) )
            || (materiel.reference_fabricant && materiel.reference_fabricant.toLowerCase().includes(value.toLowerCase()))
        )

      } 
      this.pageValue = 1
      this.paginator(this.filteredpayloadValue)
    }

    filter({target : {value}, params: {property}}) {
      if (property === 'famille') {
        this.selectedFamille = value;
      } else if (property === 'marque') {
        this.selectedMarque = value;
      }

      // Call a method to apply the filters
      this.search(null, this.searchInputTarget.value);
    }

    removeFilters() {
      this.selectedFamille = null
      this.selectedMarque = null

      this.familySelectorTarget.selectedIndex = 0
      this.brandSelectorTarget.selectedIndex = 0
      
      this.search(null, this.searchInputTarget.value);
    }

    // Handle page navigation
    paginator(payload = !this.filteredpayloadValue.length ? this.payloadValue : this.filteredpayloadValue ) {
      let page = this.pageValue || 1,
      per_page = this.elementsPerPageValue,
      offset = (page - 1) * per_page,
    
      paginatedItems = payload.slice(offset).slice(0, this.elementsPerPageValue),
      total_pages = Math.ceil(payload.length / per_page);
        
      this.tableBodyTarget.innerHTML = ''
      paginatedItems.forEach(materiel => this.addRow(materiel));

      this.totalPageIndicatorTarget.innerHTML = `${total_pages}`
      this.totalElementsIndicatorTarget.innerHTML = `${payload.length}`
      return {
        page: page,
        per_page: per_page,
        pre_page: page - 1 ? page - 1 : null,
        next_page: (total_pages > page) ? page + 1 : null,
        total: payload.length,
        total_pages: total_pages,
        data: paginatedItems
      };
    }

    // Go back to previous page
    previous() {
      if (this.pageValue > 1) {
        this.pageValue = this.pageValue - 1 
      }
    }

    // Move on to the next page
    next() {
      let pagination = this.paginator()
      if (this.pageValue < pagination.total_pages)
        this.pageValue = this.pageValue + 1
    }

    // Add a row to the table
    addRow(materiel) {
      this.tableBodyTarget.innerHTML += `
        <tr>
            <td class="px-4 py-4 text-sm font-medium whitespace-nowrap">
                <div>
                    <p class="font-medium text-gray-800 dark:text-white ">${materiel.id}</p>
                </div>
            </td>
            <td class="px-4 py-4 text-sm font-medium whitespace-nowrap">
                <div>
                    <p class="font-medium text-gray-800 dark:text-white ">${materiel.nom_court}</p>
                </div>
            </td>
            <td class="px-4 py-4 text-sm font-medium whitespace-nowrap">
                <div>
                    <p class="font-medium text-gray-800 dark:text-white ">${materiel.marque}</p>
                </div>
            </td>
            <td class="px-4 py-4 text-sm font-medium whitespace-nowrap">
                <div>
                    <p class="font-medium text-gray-800 dark:text-white ">${materiel.prix_public}€</p>
                </div>
            </td>
            <td class="px-4 py-4 text-sm font-medium whitespace-nowrap">
                <div>
                    <p class="font-medium text-gray-800 dark:text-white ">${materiel.reference_fabricant}</p>
                </div>
            </td>
            <td class="px-4 py-4 text-sm font-medium whitespace-nowrap">
                <div>
                    <p class="font-medium text-gray-800 dark:text-white ">${materiel.type.nom}</p>
                </div>
            </td>
            <td class="px-4 py-4 text-sm font-medium whitespace-nowrap">
                <div>
                    <p class="font-medium text-gray-800 dark:text-white ">${materiel.type.metier.nom}</p>
                </div>
            </td>
        </tr>
      `
    }

    // Get all unique families from the materiel objects
    getUniqueFamilies() {
        const uniqueFamilies = [...new Set(this.payloadValue.map(materiel => materiel.type.famille))];
        return uniqueFamilies;
    }

    // Get all unique brands from the materiel objects
    getUniqueBrands() {
        const uniqueBrands = [...new Set(this.payloadValue.map(materiel => materiel.marque))];
        return uniqueBrands;
    }

    // Fill the options of a <select> element
    fillOptions(selectTarget, options) {
        // selectTarget.innerHTML = '';
        options.forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option;
            optionElement.textContent = option;
            selectTarget.appendChild(optionElement);
        });
    }

}
