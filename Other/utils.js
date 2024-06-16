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


/**
 * The sidebar_sublink function creates a sublink for the sidebar.
 * 
 *
 * @param text Set the text of the link
const sidebar_link = (text, sublinks) =&gt; {
  li = document
 * @param link Create the href attribute of the a tag
 *
 * @return A li element
 *
 * @docauthor Trelent
 */
const sidebar_sublink = (text, link) => {
  
  span = document.createElement("span");
  span.innerText = text;
  i_tag = document.createElement("i");
  i_tag.className = "bi bi-circle";

  a_tag = document.createElement("a")
  a_tag.href = link;
  a_tag.appendChild(i_tag);
  a_tag.appendChild(span);
  
  li = document.createElement("li")
  li.appendChild(a_tag);
  return li;
}

const sidebar_nav_link = (text, link) => {
  span = document.createElement("span");
  span.innerText = text;
  i_tag = document.createElement("i");
  i_tag.className = "bi bi-grid";
  
  a_tag = document.createElement("a")
  a_tag.href = link;
  a_tag.className = "nav-link"
  a_tag.appendChild(i_tag);
  a_tag.appendChild(span);

  li = document.createElement("li")
  li.className = "nav-item";
  li.appendChild(a_tag);
  return li;
}

const create_nav_item = (text, sub_links="") => {
  if(Array.isArray(sub_links)){

  } else {
    console.log("String");
    li = sidebar_nav_link(text, sub_links);
    document.getElementById("sidebar-nav").appendChild(li);
  }
}

/**
 * The populate_sidebar function populates the sidebar with links to all of the
 * pages in this documentation. It does so by creating a list item for each page,
 * and appending it to an unordered list in the sidebar. The function also adds 
 * a class name &quot;active&quot; to any link that corresponds with the current page, so 
 * that we can style it differently from other links using CSS. This is done by 
 * comparing window.location (the URL of this document) against each link's href attribute; if they match, then we know we're on that page and should add &quot;active&quot;. If
 *
 *
 * @return A list of links that are used to populate the sidebar
 *
 * @docauthor Trelent
 */
const populate_sidebar = () => {
  
  sidebar_map = {
    "components-nav": {
      "Alerts": "components-alerts.html"
    }
  }
  for(let sidebar_id in sidebar_map){
    sub_link = sidebar_map[sidebar_id]
    ul = document.getElementById(sidebar_id);
    for( let title in sub_link){
      li = sidebar_sublink(title, sub_link[title]);
      ul.appendChild(li);
    }
  }

}

populate_sidebar();
create_nav_item("Clients", "clients")
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