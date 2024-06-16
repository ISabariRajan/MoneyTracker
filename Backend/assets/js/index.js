var props = {
  id: "test"
}
var tester = "22dsrsd"
// var children = ["HELLO"]
const c1 = CReact.createElement("h1", {}, tester)
const c2 = CReact.createElement("h2", {style: "text-align:right;"}, "Simple")
element = CReact.createElement("div", props, c1, c2)


children = ["HELLO", "TEST", "TTTEE"]
console.log(children, ...children)

// /** @jsx Didact.createElement */
// function App(props) {
//   return <h1>Hi {props.name}</h1>
// }
// const element = <App name="foo" />
// const container = document.getElementById("root")
// Didact.render(element, container)


// function App() {
//   console.log()
//   const [state, setState] = CReact.useState(1);
//   const incrementer = () => setState(c => c+1);
//   // incrementer();
//   const props = {
//     onClick: {incrementer}
//   }
//   return CReact.createElement(
//     "h1",
//     props,
//     `Count: ${state}`
//   )
// }

// const element = CReact.createElement(App)

const root = document.getElementById("root")
CReact.render(element, root)