<template>
    <div class="field">
        <p class="control has-icons-left is-expanded">
            <input type="text"
                   class="input animated"
                   :class="{'search-input-focused': isFocused}"
                   name="q"
                   placeholder="Search..."
                   v-model="term"
                   @blur="isFocused = false"
                   @focus="isFocused = true">
            <span class="icon is-small is-left">
                <i class="fa fa-search"></i>
            </span>
        </p>
    </div>
</template>
<script>
    export default {
        props: ['data'],

        data() {
            return {
                isFocused: false,
                term: this.data,
            };
        }
    }
</script>
<style>
    .animated {
        width: 215px;
        -webkit-transition: width .5s ease-in-out;
        -moz-transition: width .5s ease-in-out;
        -o-transition: width .5s ease-in-out;
        transition: width .5s ease-in-out;
    }
    .search-input-focused {
        width: 350px;
    }
</style>