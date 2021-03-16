<template>
  <div
    :tabindex="searchable ? -1 : tabindex"
    :class="[isOpen?'active':'', disabled?'disabled':'', size && size !== 'default'? 'multiselect-'+size : '' ]"
    @focus="activate()"
    @keydown.self.down.prevent="pointerForward()"
    @keydown.self.up.prevent="pointerBackward()"
    @keydown.enter.tab.stop.self="addPointerElement($event)"
    @keyup.esc="deactivate()"
    class="multiselect">
    <div
      ref="tags"
      class="multiselect-tags">
      <div class="multiselect-tags-list">
        <template
          v-for="option of visibleValues"
          @mousedown.prevent>
          <slot
            name="tag"
            :option="option"
            :search="search"
            :remove="removeElement">
            <div
              class="multiselect-tag"
              :class="variant?'multiselect-tag-'+variant : 'multiselect-tag-default'">
              <span v-text="getOptionLabel(option)"/>
              <i
                aria-hidden="true"
                tabindex="1"
                @keydown.enter.prevent="removeElement(option)"
                @mousedown.prevent="removeElement(option)"
                class="multiselect-tag-remove"/>
            </div>
          </slot>
        </template>
        <template v-if="internalValue && internalValue.length > limit">
          <slot name="limit">
            <div
              class="multiselect-tags-list-addon text-muted"
              v-text="limitText(internalValue.length - limit)"/>
          </slot>
        </template>
        <input
          ref="search"
          v-if="searchable"
          :name="name"
          :id="id"
          type="text"
          autocomplete="off"
          :placeholder="placeholder"
          :style="inputStyle"
          :value="search"
          :disabled="disabled"
          :tabindex="tabindex"
          @input="updateSearch($event.target.value)"
          @focus.prevent="activate()"
          @blur.prevent="deactivate()"
          @keyup.esc="deactivate()"
          @keydown.down.prevent="pointerForward()"
          @keydown.up.prevent="pointerBackward()"
          @keydown.enter.prevent.stop.self="addPointerElement($event)"
          @keydown.delete.stop="removeLastElement()"
          class="multiselect-input">
        <span
          v-if="isSingleLabelVisible"
          class="multiselect-single"
          @mousedown.prevent="toggle">
          <slot
            name="singleLabel"
            :option="singleValue">
            <template>{{ currentOptionLabel }}</template>
          </slot>
        </span>
        <slot
          name="placeholder"
          v-if="isPlaceholderVisible"
          @mousedown.prevent="toggle">
          <span class="multiselect-single">
            {{ placeholder }}
          </span>
        </slot>
      </div>
      <!--transition name="multiselect__loading">
        <slot name="loading">
          <div
            v-show="loading"
            class="multiselect__spinner"/>
        </slot>
      </transition-->
      <slot
        name="caret"
        :toggle="toggle">
        <div
          @mousedown.prevent.stop="toggle()"
          class="multiselect-tags-append multiselect-caret"/>
      </slot>
    </div>
    <div
      class="multiselet-dropdown"
      :class="dropdownClasses"
      v-show="isOpen"
      @focus="activate"
      @mousedown.prevent
      ref="list"
      role="menu">
      <slot name="beforeList"/>
      <div v-if="multiple && max === internalValue.length">
        <span class="multiselect-option">
          <slot name="maxElements">Maximum of {{ max }} options selected.</slot>
        </span>
      </div>
      <template v-if="!max || internalValue.length < max">
        <template v-for="(option, index) of filteredOptions">
          <button
            v-if="!(option && (option.$isLabel || option.$isDisabled))"
            :class="optionHighlight(index, option)"
            @click.stop="select(option)"
            @mouseenter.self="pointerSet(index)"
            :data-select="option && option.isTag ? tagPlaceholder : selectLabelText"
            :data-selected="selectedLabelText"
            :data-deselect="deselectLabelText"
            :key="index"
            class="multiselect-option"
            role="menuitem">
            <slot
              name="option"
              :option="option"
              :search="search">
              <span>{{ getOptionLabel(option) }}</span>
            </slot>
          </button>
          <button
            v-else
            :data-select="groupSelect && selectGroupLabelText"
            :data-deselect="groupSelect && deselectGroupLabelText"
            :class="groupHighlight(index, option)"
            @mouseenter.self="groupSelect && pointerSet(index)"
            @mousedown.prevent="selectGroup(option)"
            :key="index"
            class="multiselect-option multiselect-option-header"
            role="menuitem">
            <slot
              name="option"
              :option="option"
              :search="search">
              <span>{{ getOptionLabel(option) }}</span>
            </slot>
          </button>
        </template>
      </template>
      <div v-show="showNoResults && (filteredOptions.length === 0 && search && !loading)">
        <span class="multiselect-option text-muted">
          <slot name="noResult">No elements found.</slot>
        </span>
      </div>
      <slot name="afterList"/>
    </div>
  </div>
</template>

<script>
import multiselectMixin from "vue-multiselect/src/multiselectMixin";
import pointerMixin from "vue-multiselect/src/pointerMixin";
import Popper from "popper.js";


export default {
  name: "Multiselect",
  mixins: [multiselectMixin, pointerMixin],
  props: {
    /**
     * name attribute to match optional label element
     * @default ''
     * @type {String}
     */
    name: {
      type: String,
      default: ""
    },
    /**
     * String to show when pointing to an option
     * @default 'Press enter to select'
     * @type {String}
     */
    selectLabel: {
      type: String,
      default: "Press enter to select"
    },
    /**
     * String to show when pointing to an option
     * @default 'Press enter to select'
     * @type {String}
     */
    selectGroupLabel: {
      type: String,
      default: "Press enter to select group"
    },
    /**
     * String to show next to selected option
     * @default 'Selected'
     * @type {String}
     */
    selectedLabel: {
      type: String,
      default: "Selected"
    },
    /**
     * String to show when pointing to an alredy selected option
     * @default 'Press enter to remove'
     * @type {String}
     */
    deselectLabel: {
      type: String,
      default: "Press enter to remove"
    },
    /**
     * String to show when pointing to an alredy selected option
     * @default 'Press enter to remove'
     * @type {String}
     */
    deselectGroupLabel: {
      type: String,
      default: "Press enter to deselect group"
    },
    /**
     * Decide whether to show pointer labels
     * @default true
     * @type {Boolean}
     */
    showLabels: {
      type: Boolean,
      default: true
    },
    /**
     * Limit the display of selected options. The rest will be hidden within the limitText string.
     * @default 99999
     * @type {Integer}
     */
    limit: {
      type: Number,
      default: 99999
    },
    /**
     * Sets maxHeight style value of the dropdown
     * @default 300
     * @type {Integer}
     */
    maxHeight: {
      type: Number,
      default: 300
    },
    /**
     * Function that process the message shown when selected
     * elements pass the defined limit.
     * @default 'and * more'
     * @param {Int} count Number of elements more than limit
     * @type {Function}
     */
    limitText: {
      type: Function,
      default: count => `and ${count} more`
    },
    /**
     * Set true to trigger the loading spinner.
     * @default False
     * @type {Boolean}
     */
    loading: {
      type: Boolean,
      default: false
    },
    /**
     * Disables the multiselect if true.
     * @default false
     * @type {Boolean}
     */
    disabled: {
      type: Boolean,
      default: false
    },
    /**
     * Fixed opening direction
     * @default ''
     * @type {String}
     */
    openDirection: {
      type: String,
      default: ""
    },
    showNoResults: {
      type: Boolean,
      default: true
    },
    tabindex: {
      type: Number,
      default: 0
    },
    /* bootstrap vue size */
    size: {
      type: String,
      default: ""
    },
    /* bootstrap vue variant */
    variant: {
      type: String,
      default: "default"
    },
    /* bootstrap-vue popper position */
    dropup: {
      // place on top if possible
      type: Boolean,
      default: false
    },
    /* bootstrap-vue popper boundry */
    boundary: {
      // String: `scrollParent`, `window` or `viewport`
      // Object: HTML Element reference
      type: [String, Object],
      default: "scrollParent"
    },
    /* bootstrap-vue popper opts */
    popperOpts: {
      type: Object,
      default: () => {}
    }
  },
  computed: {
    isSingleLabelVisible() {
      return (
        this.singleValue &&
        (!this.isOpen || !this.searchable) &&
        !this.visibleValues.length
      );
    },
    isPlaceholderVisible() {
      return !this.internalValue.length && (!this.searchable || !this.isOpen);
    },
    visibleValues() {
      return this.multiple ? this.internalValue.slice(0, this.limit) : [];
    },
    singleValue() {
      return this.internalValue[0];
    },
    deselectLabelText() {
      return this.showLabels ? this.deselectLabel : "";
    },
    deselectGroupLabelText() {
      return this.showLabels ? this.deselectGroupLabel : "";
    },
    selectLabelText() {
      return this.showLabels ? this.selectLabel : "";
    },
    selectGroupLabelText() {
      return this.showLabels ? this.selectGroupLabel : "";
    },
    selectedLabelText() {
      return this.showLabels ? this.selectedLabel : "";
    },
    inputStyle() {
      if (
        this.searchable ||
        (this.multiple && this.value && this.value.length)
      ) {
        // Hide input by setting the width to 0 allowing it to receive focus
        return this.isOpen
          ? { width: "auto" }
          : { width: "0", position: "absolute", padding: "0" };
      }
    },
    contentStyle() {
      return this.options.length
        ? { display: "inline-block" }
        : { display: "block" };
    },
    showSearchInput() {
      return (
        this.searchable &&
        (this.hasSingleSelectedSlot &&
        (this.visibleSingleValue || this.visibleSingleValue === 0)
          ? this.isOpen
          : true)
      );
    },
    dropdownClasses() {
      let position = "";
      // Position `static` is needed to allow menu to "breakout" of the scrollParent boundaries
      // when boundary is anything other than `scrollParent`
      // See https://github.com/twbs/bootstrap/issues/24251#issuecomment-341413786
      if (this.boundary !== "scrollParent" || !this.boundary) {
        position = "position-static";
      }
      return [
        "dropdown-menu",
        this.dropup ? "dropup" : "",
        this.isOpen ? "show" : "",
        position
      ];
    }
  },
  watch: {
    isOpen(state, old) {
      if (state === old) {
        // Avoid duplicated emits
        return;
      }
      if (state) {
        this.showMenu();
      } else {
        this.hideMenu();
      }
    },
    disabled(state, old) {
      if (state !== old && state && this.isOpen) {
        // Hide dropdown if disabled changes to true
        this.isOpen = false;
      }
    },
    value() {
      if (this._popper) this._popper.update();
    }
  },
  created() {
    // Create non-reactive property
    this._popper = null;
  },
  mounted() {
    // // To keep one dropdown opened on page
    // this.listenOnRoot("bv::dropdown::shown", this.rootCloseListener);
    // // Hide when clicked on links
    // this.listenOnRoot("clicked::link", this.rootCloseListener);
    // // Use new namespaced events
    // this.listenOnRoot("bv::link::clicked", this.rootCloseListener);
  },
  methods: {
    getPopperConfig /* istanbul ignore next: can't test popper in JSDOM */() {
      let placement = "bottom-start";
      if (this.dropup) {
        // dropup + left
        placement = "top-start";
      }
      const popperConfig = {
        placement,
        modifiers: {
          offset: {
            offset: this.offset || 0
          },
          flip: {
            behavior: this.dropup
              ? ["top-start", "top", "bottom-start", "bottom"]
              : ["bottom-start", "bottom-end", "top-start", "top"],
            enabled: !this.noFlip
          }
        }
      };
      if (this.boundary) {
        popperConfig.modifiers.preventOverflow = {
          boundariesElement: this.boundary
        };
      }
      return Object.assign(popperConfig, this.popperOpts || {});
    },
    removePopper() {
      if (this._popper) {
        // Ensure popper event listeners are removed cleanly
        this._popper.destroy();
      }
      this._popper = null;
    },
    showMenu() {
      if (this.disabled) {
        return;
      }

      this.$emit("show");
      // Ensure other menus are closed
      // this.emitOnRoot("bv::dropdown::shown", this);

      // Instantiate popper.js
      let element = this.$refs.tags;
      element = element.$el || element;
      this.removePopper();
      this._popper = new Popper(
        element,
        this.$refs.list,
        this.getPopperConfig()
      );

      /* istanbul ignore else  */
      if (
        this.groupValues &&
        this.pointer === 0 &&
        this.filteredOptions.length
      ) {
        this.pointer = 1;
      }

      /* istanbul ignore else  */
      if (this.searchable) {
        if (!this.preserveSearch) this.search = "";
        this.$nextTick(() => this.$refs.search.focus());
      } else {
        this.$el.focus();
      }

      this.$emit("shown");
    },
    hideMenu() {
      if (!this.preserveSearch) this.search = "";
      this.$emit("hide");
      // this.emitOnRoot("bv::dropdown::hidden", this);
      this.$emit("hidden");
      this.removePopper();
    },
    activate() {
      if (this.disabled) {
        return;
      }
      this.isOpen = true;
    },
    /**
     * Closes the multiselect’s dropdown.
     * Sets this.isOpen to FALSE
     */
    deactivate() {
      this.isOpen = false;
    },
    rootCloseListener(vm) {
      if (vm !== this) {
        this.isOpen = false;
      }
    },
    clickOutListener() {
      this.isOpen = false;
    },
    optionHighlight(index, option) {
      return {
        focused: index === this.pointer && this.showPointer,
        selected: this.isSelected(option)
      };
    },
    groupHighlight(index, selectedGroup) {
      if (!this.groupSelect) {
        return ["disabled"];
      }

      const group = this.options.find(option => {
        return option[this.groupLabel] === selectedGroup.$groupLabel;
      });

      return {
        focused: index === this.pointer && this.showPointer,
        selected: this.wholeGroupSelected(group)
      };
    }
  }
};
</script>

<style lang="scss">
@import "~@coreui/coreui/scss/functions";
@import "~@coreui/coreui/scss/mixins";
@import "~@coreui/coreui/scss/variables";

$multy-select-padding-y: 0.375em !default;
$multy-select-padding-x: 0.75em !default;
$multy-select-height: $input-height !default;
$multy-select-line-height: $input-btn-line-height !default;
$multy-select-color: $input-color !default;
$multy-select-disabled-color: $gray-600 !default;
$multy-select-bg: $input-bg !default;
$multy-select-disabled-bg: $gray-200 !default;
$multy-select-bg-size: 8px 10px !default; // In pixels because image dimensions
$multy-select-indicator-color: $gray-800 !default;
$multy-select-indicator: str-replace(
    url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3E%3Cpath fill='#{$multy-select-indicator-color}' d='M2 0L0 2h4zm0 5L0 3h4z'/%3E%3C/svg%3E"),
    "#",
    "%23"
)
!default;
$multy-select-border-width: $input-btn-border-width !default;
$multy-select-border-color: $input-border-color !default;
$multy-select-border-radius: $border-radius !default;

$multy-select-font-size-sm: 75% !default;
$multy-select-height-sm: $input-height-sm !default;

$multy-select-font-size-lg: 125% !default;
$multy-select-height-lg: $input-height-lg !default;

.multiselect {
  outline: none;
  .multiselect-tags {
    font-size: $font-size-base;

    display: flex;
    width: 100%;
    min-height: $multy-select-height;
    line-height: $multy-select-line-height;
    color: $multy-select-color;
    vertical-align: middle;
    background: $multy-select-bg;
    border: $multy-select-border-width solid $multy-select-border-color;

    @if $enable-rounded {
      // Manually use the if/else instead of the mixin to account for iOS override
      border-radius: $input-border-radius;
    } @else {
      // Otherwise undo the iOS default
      border-radius: 0;
    }
  }
}

// the tags and search input wrapper
.multiselect-tags-list {
  display: flex;
  flex: 1 1 auto;
  flex-wrap: wrap;
  width: 1px;
  align-items: center;
  padding: $multy-select-padding-y/2 $multy-select-padding-x/2;
  text-align: left;

  .multiselect-input,
  .multiselect-single {
    position: relative;
    display: inline-block;
    border: none;
    width: calc(100%);
    transition: border 0.1s ease;
    box-sizing: border-box;
    padding: $multy-select-padding-y/2 $multy-select-padding-x/2;
    font-family: inherit;
    font-size: $font-size-base;
    line-height: $multy-select-line-height;
    touch-action: manipulation;
  }

  .multiselect-tags-list-addon {
    padding: $multy-select-padding-y/2 $multy-select-padding-x/2;
    font-family: inherit;
    font-size: $font-size-base;
    line-height: $multy-select-line-height;
  }

  .multiselect-input {
    max-width: 100%;
    &::placeholder {
      color: $input-placeholder-color;
    }
    &:focus {
      outline: none;
    }
  }

  .multiselect-placeholder {
    color: #adadad;
    display: inline-block;
    padding: $multy-select-padding-y/2 $multy-select-padding-x/2;
  }

  .multiselect-tag ~ .multiselect-input,
  .multiselect-tag ~ .multiselect-single {
    width: auto;
  }
}

.multiselect-tags-append {
  cursor: pointer;
  width: 1.5em;
  align-self: stretch;
  background: $multy-select-indicator no-repeat center;
  background-size: $multy-select-bg-size;
}

.multiselect-tag {
  margin-right: $multy-select-padding-y;
  margin-bottom: $multy-select-padding-y/2;
  margin-top: $multy-select-padding-y/2;
  display: flex;
  span {
    padding: $badge-padding-y $badge-padding-x;
    font-size: $badge-font-size;
    font-weight: $badge-font-weight;
    @include border-left-radius($badge-border-radius);
  }
  .multiselect-tag-remove {
    @include border-right-radius($badge-border-radius);
    padding: $badge-padding-y $badge-padding-x;
    font-size: $badge-font-size;
    font-weight: $badge-font-weight;
    cursor: pointer;
    align-content: center;
  }
}

@mixin multiselect-tag-variant($bg) {
  span {
    color: color-yiq($bg);
    background-color: $bg;
  }
  .multiselect-tag-remove {
    color: color-yiq($bg);
    background-color: $bg;
    @include hover-focus {
      transition: background-color ease 0.4s;
      background-color: darken($bg, 20%);
      color: color-yiq($bg);
      text-decoration: none;
    }
    &:after {
      content: "×";
      font-style: normal;
      line-height: 1em;
    }
  }
}

.multiselect-tag-default {
  @include multiselect-tag-variant($secondary);
}

@each $color, $value in $theme-colors {
  .multiselect-tag-#{$color} {
    @include multiselect-tag-variant($value);
  }
}

// //

.multiselect-option {
  display: block;
  padding: $dropdown-item-padding-y $dropdown-item-padding-x;
  clear: both;
  font-weight: $font-weight-normal;
  color: $dropdown-link-color;
  white-space: nowrap; // prevent links from randomly breaking onto new lines

  width: 100%;
  text-align: inherit;
  background-color: transparent;
  border: 0;

  &.selected {
    font-weight: bold;
  }

  &.focused {
    color: $dropdown-link-hover-color;
    text-decoration: none;
    @include gradient-bg($dropdown-link-hover-bg);
  }

  &.active,
  &:active {
    color: $dropdown-link-active-color;
    text-decoration: none;
    @include gradient-bg($dropdown-link-active-bg);
  }

  &.disabled,
  &:disabled {
    color: $dropdown-link-disabled-color;
    background-color: transparent;
    // Remove CSS gradients if they're enabled
    @if $enable-gradients {
      background-image: none;
    }
  }

  &.multiselect-option-header {
    font-size: 0.7em;
  }
}

// // states

.multiselect.disabled {
  pointer-events: none;
  .multiselect-tags {
    color: $multy-select-disabled-color;
    background-color: $multy-select-disabled-bg;
  }
}

.multiselect.active {
  z-index: 50;
  .multiselect-tags {
    border-color: $custom-select-focus-border-color;
    outline: 0;
    box-shadow: $custom-select-focus-box-shadow;

    &::-ms-value {
      // For visual consistency with other platforms/browsers,
      // suppress the default white text on blue background highlight given to
      // the selected option text when the (still closed) <select> receives focus
      // in IE and (under certain conditions) Edge.
      // See https://github.com/twbs/bootstrap/issues/19398.
      color: $input-color;
      background-color: $input-bg;
    }
  }
  .multiselect-placeholder {
    display: none;
  }
}

// // sizes

.multiselect-sm {
  .multiselect-single,
  .multiselect-input,
  .multiselect-tag,
  .multiselect-tags-list-addon {
    font-size: $multy-select-font-size-sm;
  }
  .multiselect-tags {
    min-height: $multy-select-height-sm;
  }
}

.multiselect-lg {
  .multiselect-single,
  .multiselect-input,
  .multiselect-tag,
  .multiselect-tags-list-addon {
    font-size: $multy-select-font-size-lg;
  }
  .multiselect-tags {
    min-height: $multy-select-height-lg;
  }
}

// // input group

.input-group > .multiselect {
  position: relative;
  -ms-flex: 1 1 auto;
  flex: 1 1 auto;
  width: 1%;

  &:not(:last-child) {
    .multiselect-tags {
      @include border-right-radius(0);
    }
  }
  &:not(:first-child) {
    .multiselect-tags {
      @include border-left-radius(0);
    }
  }
}
</style>
