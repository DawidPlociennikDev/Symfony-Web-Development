import { baseURL } from './config.js';
import { show } from './view.js';
import { pagination } from './pagination.js';
import { router } from './router.js';

export const getData = (event) => {

    axios.get(baseURL + router(event))
        .then(function (response) {

            const orderByName = document.getElementById("order-by-name");

            orderByName.addEventListener("click", getData);

            orderByName.style.display = 'block';

            if ( typeof orderByName.order === 'undefined' )
            orderByName.order = 'asc';
            else if ( orderByName.order == 'asc' ) orderByName.order = 'desc'
            else orderByName.order = 'asc';
            
            pagination(response.data);
            show(response.data['hydra:member']);
            // console.log(response.data);
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        });

}

