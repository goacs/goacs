import { isEmpty } from 'lodash'

export function filterToQueryString(filterObject) {
  let str = "";


  Object.keys(filterObject).forEach(key => {
    let value = filterObject[key];
    if (isEmpty(value) === false) {
      str += '&filter[' + key + ']=' + value;
    }
  });

  return str
}
