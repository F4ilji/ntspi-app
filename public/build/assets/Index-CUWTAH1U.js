import{M as y}from"./MainNavbar-5-nqk6c_.js";import{i as h,Z as b,r as t,c as p,a as o,w as r,b as e,F as m,d as k,o as a,e as w}from"./app-lT3z3TR3.js";import{F as C}from"./v3-B5UP5GZw.js";import{C as P}from"./ClientScrollTimeline-BxA_EWwM.js";import{C as A}from"./SearchModal-YI3bdWA9.js";import{A as B,a as F,b as I}from"./AdminIndexHeaderTitle-DyC17FG6.js";import{A as H}from"./AdminIndexHeader-Ci_3kRvq.js";import{C as M,a as $}from"./ClientPostSearch-85XLLeIC.js";import{C as N}from"./ClientPost-KvELvIac.js";import{E,a as L,T as S}from"./EventBuilder-Cx5Dh3-L.js";import{M as T}from"./MainPageNavbar-BfM50q-7.js";import{_ as j}from"./_plugin-vue_export-helper-DlAUqK2U.js";/* empty css                                                                   */import"./SortingByFilter-CjZ8gKSr.js";import"./ClientImageSlider-CGZZYYI8.js";import"./PageTabBuilder-CXLfoCo_.js";import"./HeadingBlock-DZAeIVzd.js";import"./ParagraphBlock-_EhfXk9v.js";import"./ImageBlock-cPU_Sbw4.js";import"./FileBlock-BU1S9yFx.js";import"./PersonBlock-MXpsfzV_.js";import"./StepperBlock-DhPHG4yx.js";import"./VideoBlock-BcEyk91j.js";import"./PostListBlock-Vo9iG3Sb.js";const D={name:"Index",components:{MainPageNavBar:T,EventBuilder:E,EventBackButton:L,TitleEvent:S,AdminIndexHeaderTitle:B,AdminIndexHeader:H,AdminIndexFilter:F,AdminIndexSearch:I,ClientFooterDown:A,ClientScrollTimeline:P,ClientPostFilter:M,Link:h,MainNavbar:y,FsLightbox:C,Head:b,ClientPost:N,ClientPostSearch:$},data(){return{}},props:{posts:{type:Array},filters:{type:Array},categories:{type:Array},navigation:{type:Array}},methods:{},mounted(){}},z={class:"flex flex-col h-screen"},V={class:"flex-grow"},Y={class:"relative mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10"},Z={class:"px-4 pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto"},q={class:"space-y-5 md:space-y-4"},G={class:"space-y-5 md:space-y-4"},J={class:"container px-8 mx-auto xl:px-5 max-w-screen-lg py-5 lg:py-8"},K={class:"mt-10 grid gap-10 md:grid-cols-2 lg:gap-10 xl:grid-cols-3"},O={class:"mt-10 flex items-center justify-center"},Q={class:"isolate inline-flex -space-x-px rounded-md shadow-sm","aria-label":"Pagination"};function R(n,s,i,U,W,X){const c=t("Head"),u=t("MainPageNavBar"),g=t("ClientPostSearch"),x=t("ClientPostFilter"),f=t("AdminIndexHeader"),_=t("ClientPost"),l=t("Link"),v=t("ClientFooterDown");return a(),p(m,null,[o(c,null,{default:r(()=>s[0]||(s[0]=[e("title",null,"Новости",-1),e("meta",{name:"description",content:"Your page description"},null,-1)])),_:1}),o(u,{class:"border-b",sections:n.$page.props.navigation},null,8,["sections"]),e("div",z,[e("main",V,[e("div",Y,[e("div",Z,[e("div",null,[o(f,null,{default:r(()=>[o(g),o(x,{items:i.categories},null,8,["items"])]),_:1}),e("div",q,[e("div",G,[e("div",null,[e("div",J,[e("div",K,[(a(!0),p(m,null,k(i.posts.data,d=>(a(),w(_,{key:d.id,post:d},null,8,["post"]))),128))]),e("div",O,[e("nav",Q,[o(l,{as:"button",href:n.$props.posts.links.prev,disabled:n.$props.posts.links.prev===null,class:"relative inline-flex items-center gap-1 rounded-l-md border border-gray-300 bg-white px-3 py-2 pr-4 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20 disabled:pointer-events-none disabled:opacity-40 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300"},{default:r(()=>s[1]||(s[1]=[e("svg",{xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor","aria-hidden":"true","data-slot":"icon",class:"h-3 w-3"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M15.75 19.5 8.25 12l7.5-7.5"})],-1),e("span",null,"Предыдущая",-1)])),_:1},8,["href","disabled"]),o(l,{as:"button",href:n.$props.posts.links.next,disabled:n.$props.posts.links.next===null,class:"relative inline-flex items-center gap-1 rounded-r-md border border-gray-300 bg-white px-3 py-2 pl-4 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20 disabled:pointer-events-none disabled:opacity-40 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300"},{default:r(()=>s[2]||(s[2]=[e("span",null,"Следующая",-1),e("svg",{xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor","aria-hidden":"true","data-slot":"icon",class:"h-3 w-3"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"m8.25 4.5 7.5 7.5-7.5 7.5"})],-1)])),_:1},8,["href","disabled"])])])])])])])])])])]),o(v)])],64)}const Ce=j(D,[["render",R]]);export{Ce as default};