import{_ as n}from"./_plugin-vue_export-helper-DlAUqK2U.js";import{o as r,c as o,b as t,t as a,n as d,f as s,F as c,d as m}from"./app-lWrE2aWG.js";const u={name:"EmailBlock",data(){return{}},methods:{},props:{block:{type:Object},error:{type:Object}}},b={class:"mb-4 sm:mb-8"},_=["for"],f={class:"relative"},k=["required","name","min","max","id","placeholder"],x={key:0,class:"absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3"},h={key:0,class:"mt-2 text-sm text-gray-500",id:"hs-input-helper-text"},y={class:"text-sm text-red-600 mt-2",id:"hs-validation-name-error-helper"};function g(v,l,e,w,p,B){return r(),o("div",b,[t("label",{for:e.block.data.name_field+"-id",class:"block mb-2 text-sm font-medium"},a(e.block.data.title_field),9,_),t("div",f,[t("input",{required:e.block.data.rules.required,name:e.block.data.name_field,min:e.block.data.rules.min,max:e.block.data.rules.max,type:"email",id:e.block.data.name_field+"-id",class:d([e.error?"border-red-500 focus:border-red-500 focus:ring-red-500":"focus:border-blue-500 focus:ring-blue-500","py-3 px-4 block w-full border-gray-200 rounded-lg text-sm disabled:opacity-50 disabled:pointer-events-none"]),placeholder:e.block.data.title_field},null,10,k),e.error?(r(),o("div",x,l[0]||(l[0]=[t("svg",{class:"shrink-0 size-4 text-red-500",xmlns:"http://www.w3.org/2000/svg",width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round","stroke-linejoin":"round"},[t("circle",{cx:"12",cy:"12",r:"10"}),t("line",{x1:"12",x2:"12",y1:"8",y2:"12"}),t("line",{x1:"12",x2:"12.01",y1:"16",y2:"16"})],-1)]))):s("",!0)]),e.error?s("",!0):(r(),o("p",h,a(e.block.data.description),1)),(r(!0),o(c,null,m(e.error,i=>(r(),o("p",y,a(i),1))),256))])}const C=n(u,[["render",g]]);export{C as default};