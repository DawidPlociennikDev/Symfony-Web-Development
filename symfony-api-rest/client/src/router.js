var array = ['?myParam=1','','',''];
export function router(event)
{
    if( typeof event.target.order !== 'undefined'  )
    {
        array[1] =  '&order[name]='+event.target.order;
        return '/api/products'+array[0]+array[1]+array[2]+array[3];
    }
    else if (typeof event.target.nextLink !== 'undefined')
    {
        return event.target.nextLink;
    }
    else 
    {
        array[1] = '';
        array[2] = '';
        array[3] = '';
        return '/api/products?myParam=true';
    }
}

