(self["webpackChunk"]=self["webpackChunk"]||[]).push([[428],{52290:function(e,t,n){"use strict";n.r(t),n.d(t,{default:function(){return E}});var s=n(78099),a=n(67294),l=n(57337),r=n(30724),o=n.n(r),i=n(10043),c=n.n(i),d=n(63056),u=n(3182),h=(n(74379),n(25740)),p=n(94043),m=n.n(p),f=n(11238),v=(0,f.l7)({prefix:"/api/docs/",timeout:1e4,headers:{"Content-Type":"application/json"},errorHandler:function(e){h.default.error({message:e.message})}});v.interceptors.response.use(function(){var e=(0,u.Z)(m().mark((function e(t){var n;return m().wrap((function(e){while(1)switch(e.prev=e.next){case 0:return e.next=2,t.clone().json();case 2:return n=e.sent,console.log("response data",n),0!==n.code&&(h.default.error({message:n.message,description:n.description}),Promise.reject(n)),e.abrupt("return",t);case 6:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}());var x=v,g={getMenu:"docs/get_menu",getContent:"docs/get_content"},y=n(85893);function j(){var e=(0,a.useState)(""),t=(0,l.Z)(e,2),n=t[0],s=t[1],r=(0,a.useState)(!1),i=(0,l.Z)(r,2),u=i[0],h=i[1];return(0,a.useEffect)((()=>{var e,t;console.log("markdown.tsx:::history",d.m8.location.query);var n=location.pathname.split("/"),a=n[n.length-1],l=null===(e=d.m8.location.query)||void 0===e?void 0:e.key;null!==(t=d.m8.location.query)&&void 0!==t&&t.key&&!u&&(h(!0),x.post(g.getContent,{data:{type:a,key:l}}).then((e=>{h(!1),s(e.data&&e.data.content?e.data.content:"\u6682\u65e0\u6570\u636e")})).catch((e=>{h(!1)})))}),[d.m8.location.query]),(0,y.jsx)(o(),{plugins:[c()],className:"markdown-body",children:n})}function k(e){return(0,y.jsx)("div",{style:{padding:"15px 30px"},children:(0,y.jsx)(j,{})})}n(3317);var S=n(67866),Z=(n(32157),n(58894)),b=n(27905),w=Z.Z.DirectoryTree,N=e=>{var t=e.title,n=e.subTitle,s=(e.multi,n?"tree-title-multi-inline":"tree-title-single");return(0,y.jsxs)("div",{className:s,children:[(0,y.jsx)("p",{children:t}),(0,y.jsx)("p",{children:n})]})};function C(e){var t=(0,a.useState)([]),n=(0,l.Z)(t,2),s=n[0],r=n[1],o=(0,a.useState)([]),i=(0,l.Z)(o,2),c=(i[0],i[1]),u=((0,b.TH)(),(0,a.useState)(!1)),h=(0,l.Z)(u,2),p=h[0],m=h[1],f=(0,a.useState)([]),v=(0,l.Z)(f,2),x=(v[0],v[1]),g=(0,a.useState)([]),j=(0,l.Z)(g,2),k=j[0],S=j[1],Z=e=>{console.log("onExpand",e),S(e),c(e),setTimeout((()=>{m(!0)}),200)},C=e=>{console.log("onCheck",e),x(e)},T=e=>{console.log(e,"cxss");for(var t="",n=0;n<e.length;n++){if(e[n].children){t=T(e[n].children);break}t=e[n].key;break}return t},E=e=>{d.m8.push({query:{key:e}}),S([e]),Z([e])},q=(e,t)=>{console.log("onSelect",t),t.node.isLeaf&&E(e[0])};return(0,a.useEffect)((()=>{e.selected&&(c([e.selected]),S([e.selected])),console.log(e.selected,"\u9009\u62e9")}),[e.selected]),(0,a.useEffect)((()=>{m(!1),r(e.items);var t=T(e.items);E(t),Z([t]),console.log(t,"\u9009\u62e9222")}),[e.items]),(0,y.jsx)(y.Fragment,{children:p&&s.length>0?(0,y.jsx)(w,{onExpand:Z,onCheck:C,onSelect:q,className:"sidebar",treeData:s,defaultExpandAll:p,titleRender:e=>(0,y.jsx)(N,{title:e.title,subTitle:e.subTitle,multi:null===e||void 0===e?void 0:e.children}),selectedKeys:k}):""})}function T(){var e=(0,a.useState)([]),t=(0,l.Z)(e,2),n=t[0],s=t[1],r=(0,a.useState)([]),o=(0,l.Z)(r,2),i=o[0],c=o[1],u=(0,a.useState)(void 0),h=(0,l.Z)(u,2),p=h[0],m=h[1],f=[],v=e=>{for(var t=0;t<e.length;t++)e[t].isLeaf&&f.push({label:e[t].key,value:e[t].key}),e[t].children&&v(e[t].children),t==e.length-1&&c(f)},j=e=>{m(e),e&&d.m8.push({query:{key:e}}),console.log(e,"skks")};return(0,a.useEffect)((()=>{var e=location.pathname.split("/"),t=e[e.length-1];x.post(g.getMenu,{data:{type:t}}).then((e=>{c([]),m(void 0),s(e.data),v(e.data)}))}),[d.m8.location.pathname]),(0,y.jsxs)("div",{className:"sidebar withMargin panel",children:[(0,y.jsx)("div",{className:"menuSearch",children:(0,y.jsx)(S.Z,{showSearch:!0,placeholder:"\u641c\u7d22",options:i,value:p,allowClear:!0,onChange:j,style:{width:"100%",textAlign:"left"}})}),(0,y.jsx)("div",{className:"menuStyle",children:(0,y.jsx)("div",{className:"leftTree",children:(0,y.jsx)(C,{items:n,selected:p})})})]})}function E(){(0,a.useState)("modules");return(0,y.jsx)("div",{className:"container",children:(0,y.jsxs)("div",{className:"body",children:[(0,y.jsx)(s.Z,{direction:"e",style:{flexGrow:"0.7"},children:(0,y.jsx)(T,{})}),(0,y.jsx)("div",{className:"content panel",children:(0,y.jsx)(k,{})})]})})}}}]);