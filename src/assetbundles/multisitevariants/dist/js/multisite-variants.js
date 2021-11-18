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
     });
 
 });
 document.querySelector('#variant-multi-site').remove();
 
 