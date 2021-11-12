/**
 * Multisite Variants plugin for Craft CMS 3.x
 *
 * Multisite Variants JS
 *
 * @author    Web DNA
 * @copyright Copyright (c) 2021 Web DNA
 * @link      https://webdna.co.uk
 * @package   MultisiteVariants
 * @since     1.0.0
 */

let variantBlocks = document.querySelectorAll(`.variant-matrixblock[data-id]`);

variantBlocks.forEach( b => {
    let variantId = b.dataset.id,
        $metaFields = b.querySelector(`.fields .variant-properties.meta`),
        $stockInput = $metaFields.querySelector(`input#variants-${ variantId }-stock`),
        $unlimitedCheck = $metaFields.querySelector(`input#variants-${ variantId }-unlimited-stock`),
        $switch = document.querySelector(`#variants-${ variantId }-enabledForSite-field`),
        stock = document.querySelector(`input[name="variants[${ variantId }][siteStock]"]`).value,
        unlimited = !!document.querySelector(`input[name="variants[${ variantId }][siteHasUnlimitedStock]"]`).value;
        console.log(unlimited);
        // Add switch
        $metaFields.appendChild($switch);
        // Modify stock
        $stockInput.value = stock;
        $stockInput.disabled = unlimited;
        $unlimitedCheck.checked = unlimited;
        if (unlimited) {
            $stockInput.classList.add('disabled');
        } else {
            $stockInput.classList.remove('disabled');
        }        
});
