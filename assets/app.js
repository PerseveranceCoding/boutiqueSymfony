/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';


// or, specify which plugins you need:
import { Tooltip, Toast, Popover } from 'bootstrap';

// start the Stimulus application
import './bootstrap';
import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';

const slider = document.getElementById('price-slider');

if(slider){
    const min = document.getElementById('min')
    const max = document.getElementById('max')
    const range = noUiSlider.create(slider, {
        start: [min.value || 0, max.value || 100],
        connect: true,
        step: 1,
        range: {
            'min': 0,
            'max': 100
        }
    })
    range.on('slide', function (values, handle){
        if(handle === 0){
            min.value = Math.round(values[0])
        }
        if(handle === 1){
            max.value = Math.round(values[1])
        }
    })
    
}








