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
         $multiFields = document.querySelector(`#variants-${ variantId }-multi-site`).children;
     Array.from($multiFields).forEach(el => {
         $metaFields.appendChild(el);
         if (el.id.indexOf('stock') !== -1) {

            //  lets instantiate the stock fields
            let checkbox = el.querySelector('input.unlimited-stock.checkbox');
            checkbox.addEventListener("change", function(i) {
                let input = el.querySelector('.textwrapper input');
                console.log(input);
                if (checkbox.checked == true) {
                    input.disabled = true;
                    input.classList.add('disabled');
                    // input.value = '';
                } else {
                    input.disabled = false;
                    input.classList.remove('disabled');
                }
            }); 
         }

     });
 
 });
 document.querySelector('#variant-multi-site').remove();

 
 