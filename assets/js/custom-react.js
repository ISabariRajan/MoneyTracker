console.log(new Date())

const is_event = key => key.startsWith("on")
const is_property = key => key !== "children" && !is_event(key)
const is_new = (prev, next) => key => prev[key] != next[key]
const is_gone = (prev, next) => key => !(key in next)

// Creates an TEXT_ELEMENT object for texts
const createTextElement = (text) => {
  return {
    type: "TEXT_ELEMENT",
    props: {
      nodeValue: text,
      children: []
    }
  }
}

// Creates element object by props and children
const createElement = (type, props, ...children) => {
  console.log(`${type} ..... ${props}   .... ${children}`)
  return {
    type,
    props: {
      ...props,
      children: children.map(child => 
        typeof child === "object"
        ? child
        : createTextElement(child)
      )
    },
  }
}

// Create the DOM element by props and type
const create_dom = (fiber) => {

  const dom =
  fiber.type == "TEXT_ELEMENT"
    ? document.createTextNode("")
    : document.createElement(fiber.type);

  // const is_property = key => key !== "children"
  Object.keys(fiber.props)
    .filter(is_property)
    .forEach(name => {
      dom[name] = fiber.props[name]
    })

  return dom
}

const update_dom = (dom, old_props, new_props) => {

  // Reve old or changed event listeners
  Object.keys(old_props)
    .filter(is_event)
    .filter(key =>
        !(key in new_props) ||
        is_new(old_props, new_props)(key)
    )
    .forEach(name => {
      const event_type = name.toLowerCase()
              .substring(2)
      dom.removeEventListener(
        event_type,
        old_props[name]
      )
    })

  // Remove old properties
  Object.keys(old_props)
    .filter(is_property)
    .filter(is_gone(old_props, new_props))
    .forEach(name => {
      dom[name] = ""
    })
  

  // Set new properties
  Object.keys(new_props)
    .filter(is_property)
    .filter(is_new(old_props, new_props))
    .forEach(name => {
      dom[name] = new_props[name]
    })

  // Add new Event Handlers
  Object.keys(new_props)
    .filter(is_event)
    .filter(is_new(old_props, new_props))
    .forEach(name => {
      const event_type = name
              .toLowerCase()
              .substring(2)
      dom.addEventListener(
          event_type,
          old_props[name]
        )
  })
}

const commit_root = () => {
  deletions.forEach(commit_work)
  commit_work(wip_root.child);
  current_root = wip_root;
  wip_root = null;
}

const commit_work = (fiber) => {
  if(!fiber) return

  let dom_parent_fiber = fiber.parent;
  // Loop through until we get a parent fiber
  // Functional Components doesn't DOM nodes
  while(!dom_parent_fiber.dom) dom_parent_fiber = dom_parent_fiber.parent;
  const dom_parent = dom_parent_fiber.dom;

  if(
    fiber.effect_tag === "PLACEMENT" &&
    fiber.dom != null
  ) {
    dom_parent.appendChild(fiber.dom);
  } else if (
    fiber.effect_tag === "UPDATE" &&
    fiber.dom != null
  ) {
    update_dom(
      fiber.dom,
      fiber.alternate.props,
      fiber.props
    )
  } else if(fiber.effect_tag === "DELETION"){
    commit_deletetion(fiber, dom_parent);
  }

  commit_work(fiber.child);
  commit_work(fiber.sibling);
}

const commit_deletetion = (fiber, dom_parent) => {
  if(fiber.dom) dom_parent.removeChild(fiber.dom)
  else commit_deletetion(fiber.child, dom_parent)
}

const render = (element, container) => {

  wip_root = {
    dom: container,
    props: {
      children: [element]
    },
    alternate: current_root
  }
  deletions = [];
  next_work = wip_root;
}

const work_loop = (deadline) => {
  let should_yeild = false;
  while(next_work && !should_yeild) {
    next_work = perform_work(next_work);
    should_yeild = deadline.timeRemaining() < 1;
  }
  if(!next_work && wip_root){
    commit_root();
  }

  requestIdleCallback(work_loop);
}

const perform_work = (fiber) => {

  const is_functional_component = fiber.type instanceof Function;
  if(is_functional_component){
    update_functional_component(fiber)
  } else {
    update_dom_component(fiber)
  }

  if(fiber.child){
   return fiber.child; 
  }

  let next_fiber = fiber;
  while(next_fiber){
    if(next_fiber.sibling){
      return next_fiber.sibling;
    }
    next_fiber = next_fiber.parent;
  }
}

const update_functional_component = (fiber) => {
  wip_fiber = fiber;
  hook_index = 0;
  wip_fiber.hooks = []
  const children = [fiber.type(fiber.props)]
  reconcile_children(fiber, children)
}

const useState = (initial) => {
  const old_hook = 
    wip_fiber.alternate &&
    wip_fiber.alternate.hooks &&
    wip_fiber.alternate.hooks[hook_index]
  
  const hook = {
    state: old_hook ? old_hook.state : initial,
    queue: []
  }

  const actions = old_hook ? old_hook.queue : []
  actions.forEach(action => {
    hook.state = action(hook.state)
  })

  const setState = action => {
    hook.queue.push(action)
    wip_root = {
      dom: current_root.dom,
      props: current_root.props,
      alternate: current_root
    }
    next_work = wip_root
    deletions = []
  }

  wip_fiber.hooks.push(hook);
  hook_index++;
  return [hook.state, setState]
}

const update_dom_component = (fiber) => {
  if(!fiber.dom){
    fiber.dom = create_dom(fiber)
  }
  
  const elements = fiber.props.children;
  reconcile_children(fiber, elements);
}

const reconcile_children = (wip_fiber, elements) => {

  let index = 0;
  let prev_sibling = null;
  let old_fiber = wip_fiber.alternate && wip_fiber.alternate.child;

  while(
    index < elements.length ||
    old_fiber != null
  ) {
    const element = elements[index];
    let new_fiber = null;

    const same_type =
      old_fiber &&
      element &&
      element.type == old_fiber.type;
    
    if(same_type){
      new_fiber = {
        type: old_fiber.type,
        props: element.props,
        dom: old_fiber.dom,
        parent: wip_fiber,
        alternate: old_fiber,
        effect_tag: "UPDATE"
      }
    }

    if(element && !same_type){
      new_fiber = {
        type: element.type,
        props: element.props,
        dom: null,
        parent: wip_fiber,
        alternate: null,
        effect_tag: "PLACEMENT"
      }
    }

    if(old_fiber && !same_type){
      old_fiber.effect_tag = "DELETION"
      deletions.push(old_fiber);
    }

    if(index === 0){
      wip_fiber.child = new_fiber
    } else {
      prev_sibling.sibling = new_fiber
    }
    prev_sibling = new_fiber;
    index++;

  }
}



const CReact = {
  createElement,
  render,
  useState
}


// Tracks root of fiber tree, to avoid interuption from browser
let wip_root = null;
let next_work = null;
// Used to compare current root, To update and delete fibers within
let current_root = null;
// Keep track of fibers to delete
let deletions = null;
// Used to track and manage hooks
let wip_fiber = null;
let hook_index = null;
requestIdleCallback(work_loop);