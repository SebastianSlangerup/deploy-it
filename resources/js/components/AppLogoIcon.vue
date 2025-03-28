<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg width="100%" height="100%" viewBox="0 0 999 931" xmlns="http://www.w3.org/2000/svg" :class="className" v-bind="$attrs">
        <defs>
            <linearGradient x1="92.827%" y1="0%" x2="53.422%" y2="80.087%" id="hero-shape-a">
                <stop stop-color="#F9425F" offset="0%"></stop>
                <stop stop-color="#F97C58" stop-opacity="0" offset="100%"></stop>
            </linearGradient>
            <linearGradient x1="92.827%" y1="0%" x2="53.406%" y2="80.12%" id="hero-shape-b">
                <stop stop-color="#47A1F9" offset="0%"></stop>
                <stop stop-color="#F9425F" stop-opacity="0" offset="80.532%"></stop>
                <stop stop-color="#FDFFDA" stop-opacity="0" offset="100%"></stop>
            </linearGradient>
            <linearGradient x1="8.685%" y1="23.733%" x2="85.808%" y2="82.837%" id="hero-shape-c">
                <stop stop-color="#FFF" stop-opacity=".48" offset="0%"></stop>
                <stop stop-color="#FFF" stop-opacity="0" offset="100%"></stop>
            </linearGradient>
            <linearGradient x1="79.483%" y1="15.903%" x2="38.42%" y2="70.124%" id="hero-shape-d">
                <stop stop-color="#47A1F9" offset="0%"></stop>
                <stop stop-color="#FDFFDA" stop-opacity="0" offset="100%"></stop>
            </linearGradient>
            <linearGradient x1="99.037%" y1="26.963%" x2="24.582%" y2="78.557%" id="hero-shape-e">
                <stop stop-color="#FDFFDA" stop-opacity=".64" offset="0%"></stop>
                <stop stop-color="#F97C58" stop-opacity=".24" offset="42.952%"></stop>
                <stop stop-color="#F9425F" stop-opacity="0" offset="100%"></stop>
            </linearGradient>
        </defs>
        <g fill="none" fill-rule="evenodd">
            <g class="hero-shape-top">
                <g class="is-moving-object is-translating" data-translating-factor="280">
                    <path
                        d="M680.188 0c-23.36 69.79-58.473 98.3-105.34 85.531-70.301-19.152-189.723-21.734-252.399 91.442-62.676 113.175-144.097 167.832-215.195 118.57C59.855 262.702 24.104 287.85 0 370.988L306.184 566.41c207.164-4.242 305.67-51.612 295.52-142.11-10.152-90.497 34.533-163.55 134.054-219.16l4.512-119.609L680.188 0z"
                        fill="url(#hero-shape-a)"
                        transform="translate(1)"
                    ></path>
                </g>
                <g class="is-moving-object is-translating" data-translating-factor="100">
                    <path
                        d="M817.188 222c-23.36 69.79-58.473 98.3-105.34 85.531-70.301-19.152-189.723-21.734-252.399 91.442-62.676 113.175-144.097 167.832-215.195 118.57-47.399-32.841-83.15-7.693-107.254 75.445L443.184 788.41c207.164-4.242 305.67-51.612 295.52-142.11-10.152-90.497 34.533-163.55 134.054-219.16l4.512-119.609L817.188 222z"
                        fill="url(#hero-shape-b)"
                        transform="rotate(-53 507.635 504.202)"
                    ></path>
                </g>
            </g>
            <g transform="translate(191 416)">
                <g class="is-moving-object is-translating" data-translating-factor="50">
                    <circle fill="url(#hero-shape-c)" cx="336" cy="190" r="190"></circle>
                </g>
                <g class="is-moving-object is-translating" data-translating-factor="80">
                    <path
                        d="M683.766 133.043c-112.048-90.805-184.688-76.302-217.92 43.508-33.23 119.81-125.471 124.8-276.722 14.972-3.156 120.356 53.893 200.09 171.149 239.203 175.882 58.67 346.695-130.398 423.777-239.203 51.388-72.536 17.96-92.03-100.284-58.48z"
                        fill="url(#hero-shape-d)"
                    ></path>
                </g>
                <g class="is-moving-object is-translating" data-translating-factor="100">
                    <path
                        d="M448.206 223.247c-97.52-122.943-154.274-117.426-170.26 16.55C261.958 373.775 169.717 378.766 1.222 254.77c-9.255 95.477 47.794 175.211 171.148 239.203 185.032 95.989 424.986-180.108 424.986-239.203 0-39.396-49.717-49.904-149.15-31.523z"
                        fill="url(#hero-shape-e)"
                        transform="matrix(-1 0 0 1 597.61 0)"
                    ></path>
                </g>
            </g>
        </g>
    </svg>
</template>
