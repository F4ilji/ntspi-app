import{M as v}from"./MainNavbar-Ox7xJRDB.js";import{i as b,Z as B,r as e,c as a,a as o,w as C,b as t,t as n,F as m,d as k,o as i}from"./app-lWrE2aWG.js";import{F as w}from"./v3-CDJmn87G.js";import{C as S}from"./ClientScrollTimeline-9x6SH9Qu.js";import{C as T}from"./SearchModal-CGHtjMJb.js";import F from"./ClientImageSlider-ZnyNtD9R.js";import{P as M,a as L,b as N,c as A,d as D}from"./PostGallery-BpzMY_Fm.js";import{P as H}from"./PostBuilder-BikqjS2v.js";import{P as G}from"./EventBadgeBuilder-vc1rfcET.js";import{a as R,C as j}from"./ClientPostSearch-DByct-eL.js";import{C as I}from"./ClientPost-HujX0ISt.js";import{A as O}from"./AdminIndexHeader-BLYLBYAX.js";import{M as V}from"./MainPageNavbar-BsdceJwT.js";import{_ as E}from"./_plugin-vue_export-helper-DlAUqK2U.js";/* empty css                                                                   */import"./SortingByFilter-DAe1p4QU.js";const Y={name:"Show",components:{MainPageNavBar:V,AdminIndexHeader:O,ClientPost:I,ClientPostSearch:R,ClientPostFilter:j,PostBadge:G,PostGallery:M,PostBuilder:H,PostTimeRead:L,PostAuthorsList:N,PostBackButton:A,PostTitle:D,ClientImageSlider:F,ClientFooterDown:T,ClientScrollTimeline:S,Link:b,MainNavbar:v,FsLightbox:w,Head:B},data(){return{blocks:this.post.data.content}},props:{post:{type:Object},navigation:{type:Object},searchMatch:{type:String,default:""}},mounted(){},methods:{}},Z={class:"flex flex-col h-screen"},q={class:"flex-grow"},z={class:"relative mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10"},J={class:"max-w-3xl lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto"},K={class:"space-y-5 md:space-y-10"},Q={class:"space-y-3"},U={class:"flex space-x-3 text-gray-500"},W={class:"flex items-center gap-3"},X={class:"md:flex md:items-center space-y-2 md:space-y-0 md:space-x-4 text-sm"},$={class:"block text-gray-500"},tt={class:"space-y-3 md:space-y-4"},et=["href"];function ot(l,r,s,st,at,nt){const d=e("Head"),p=e("ClientScrollTimeline"),_=e("MainPageNavBar"),u=e("PostBackButton"),f=e("PostTitle"),g=e("PostAuthorsList"),h=e("PostTimeRead"),x=e("PostBuilder"),P=e("PostGallery"),y=e("ClientFooterDown");return i(),a(m,null,[o(d,null,{default:C(()=>[t("title",null,n(s.post.data.title),1),r[0]||(r[0]=t("meta",{name:"description",content:"Your page description"},null,-1))]),_:1}),o(p),o(_,{class:"border-b",sections:l.$page.props.navigation},null,8,["sections"]),t("div",Z,[t("main",q,[t("div",z,[t("div",J,[t("div",null,[t("div",K,[t("div",Q,[o(u,{title:"Назад"}),o(f,{header:s.post.data.title},null,8,["header"])]),t("div",U,[t("div",W,[t("div",X,[o(g,{authors:s.post.data.authors},null,8,["authors"]),t("time",$,"Опубликовано "+n(s.post.data.created_post),1),o(h,{time:s.post.data.reading_time},null,8,["time"])])])]),t("div",tt,[o(x,{blocks:this.blocks},null,8,["blocks"])]),t("div",null,[o(P,{title:s.post.data.title,images:s.post.data.gallery},null,8,["title","images"])]),t("div",null,[(i(!0),a(m,null,k(s.post.data.tags,c=>(i(),a("a",{href:l.route("client.post.index",{"tag[]":c.slug}),class:"m-1 inline-flex items-center gap-1.5 py-2 px-3 rounded-full text-sm bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"},n(c.name),9,et))),256))])])])])])]),o(y)])],64)}const bt=E(Y,[["render",ot]]);export{bt as default};