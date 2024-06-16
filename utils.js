function capitalize(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

const create_table = () => {
  return document.createElement("table")
}

const create_thead = () => {
  return document.createElement("thead")
}

const create_tr = () => {
  return document.createElement("tr")
}

const create_td = () => {
  return document.createElement("td")
}

const create_h1 = () => {
  return document.createElement("h1")
}

const create_table_columns_from_data = (data, table_id) => {
  columns = []
  tr = create_tr()
  for(var key in data){
    columns.push({data: key})
    td = create_td()
    td.innerText = key
    tr.appendChild(td)
  }
  thead = create_thead()
  thead.appendChild(tr)
  table = create_table()
  table.appendChild(thead)
  table.id = table_id
  console.log(table)
  return {
    "columns": columns,
    "table": table
  }
}