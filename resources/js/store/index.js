import { createStore } from 'vuex';

export default createStore({
    state: {
        lastSlider: null, // Состояние для отслеживания положения слайдера
    },
    mutations: {
        setLastSlider(state, value) {
            state.lastSlider = value; // Мутация для изменения состояния
        },
    },
    actions: {
        updateLastSlider({ commit }, value) {
            commit('setLastSlider', value); // Действие для обновления состояния
        },
    },
    getters: {
        lastSlider: (state) => state.lastSlider, // Геттер для получения состояния
    },
});