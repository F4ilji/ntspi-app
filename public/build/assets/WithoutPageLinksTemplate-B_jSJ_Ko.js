import{i as g,Z as u,r as a,c as _,a as t,w as b,b as e,F as P,o as f,t as h}from"./app-lWrE2aWG.js";import{F as x}from"./v3-CDJmn87G.js";import{C as v}from"./SearchModal-CGHtjMJb.js";import{P as y,a as B,b as w,c as N}from"./PageNavigateLinks-C0dfJuyt.js";import{P as k}from"./PageSubSectionLinks-DpXoUZcw.js";import{M as F}from"./MainPageNavbar-BsdceJwT.js";import{_ as L}from"./_plugin-vue_export-helper-DlAUqK2U.js";const C={name:"Page",data(){return{headerNavs:this.page.data.content.filter(s=>s.type==="heading").map(s=>({id:s.data.id,text:s.data.content}))}},props:{navigation:{type:Object},page:{type:Object},subSectionPages:{type:Object},breadcrumbs:{type:Object}},components:{MainPageNavBar:F,PageSubSectionLinks:k,PageNavigateLinks:y,PageTitle:B,PageBreadcrumbs:w,PageBuilder:N,ClientFooterDown:v,Link:g,FsLightbox:x,Head:u},methods:{},computed:{}},j={class:"flex flex-col h-screen"},D={class:"flex-grow"},M={class:"relative mb-auto mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10"},O={class:"w-full min-w-0 mt-1 max-w-6xl px-1 md:px-6",style:{}},S={class:"space-y-5 md:space-y-5"},T={id:"page-area",class:"space-y-4"};function H(s,n,o,V,E,W){const i=a("Head"),r=a("MainPageNavBar"),c=a("PageNavigateLinks"),l=a("PageBreadcrumbs"),d=a("PageTitle"),m=a("PageBuilder"),p=a("ClientFooterDown");return f(),_(P,null,[t(i,null,{default:b(()=>[e("title",null,h(o.page.data.title),1),n[0]||(n[0]=e("meta",{name:"description",content:"Your page description"},null,-1))]),_:1}),e("div",j,[t(r,{class:"border-b",sections:s.$page.props.navigation},null,8,["sections"]),e("main",D,[e("div",M,[t(c,{"header-navs":this.headerNavs},null,8,["header-navs"]),e("article",O,[e("div",S,[t(l,{breadcrumbs:o.breadcrumbs,"page-title":o.page.data.title},null,8,["breadcrumbs","page-title"]),t(d,{header:o.page.data.title},null,8,["header"]),e("div",T,[t(m,{blocks:this.page.data.content},null,8,["blocks"])])])])])]),t(p)])],64)}const I=L(C,[["render",H]]);export{I as default};