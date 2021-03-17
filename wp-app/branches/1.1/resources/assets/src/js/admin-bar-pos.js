'use strict';

let adminBarPosEl = {}

const adminBarPosition = (el, i) => {
  const body = document.querySelector('body.admin-bar')

  if (!body) {
    return
  }

  const floating = ['absolute', 'fixed'],
      style = el.currentStyle || window.getComputedStyle(el)

  if (adminBarPosEl[i] === undefined) {
    adminBarPosEl[i] = {
      'position': style.position || 'static'
    };
  }

  if (floating.indexOf(adminBarPosEl[i]['position']) === 1) {
    if (adminBarPosEl[i]['top'] === undefined) {
      adminBarPosEl[i]['top'] = parseInt(style.marginTop) || 0
    }

    if (window.matchMedia('(max-width: 600px)').matches) {
      if (window.scrollY > 46) {
        el.style.position = adminBarPosEl[i]['position']
        el.style.top = adminBarPosEl[i]['top'] + 'px'
      } else {
        el.style.position = 'absolute'
        el.style.top = adminBarPosEl[i]['top'] + 46 + 'px'
      }
    } else if (window.matchMedia('(max-width: 782px)').matches) {
      el.style.position = adminBarPosEl[i]['position']
      el.style.top = adminBarPosEl[i]['top'] + 46 + 'px'
    } else {
      el.style.position = adminBarPosEl[i]['position']
      el.style.top = adminBarPosEl[i]['top'] + 32 + 'px'
    }

  } else {
    if (adminBarPosEl[i]['marginTop'] === undefined) {
      adminBarPosEl[i]['marginTop'] = parseInt(style.marginTop) || 0
    }
    if (adminBarPosEl[i]['paddingTop'] === undefined) {
      adminBarPosEl[i]['paddingTop'] = parseInt(style.paddingTop) || 0
    }

    if (window.matchMedia('(max-width: 600px)').matches) {
      if (adminBarPosEl[i]['breakpoint'] !== 600) {
        adminBarPosEl[i]['breakpoint'] = 600
        el.style.marginTop = adminBarPosEl[i]['marginTop'] + 'px'
        el.style.paddingTop = adminBarPosEl[i]['paddingTop'] + 46 + 'px'
      }
    } else if (window.matchMedia('(max-width: 782px)').matches) {
      if (adminBarPosEl[i]['breakpoint'] !== 782) {
        adminBarPosEl[i]['breakpoint'] = 782
        el.style.marginTop = adminBarPosEl[i]['marginTop'] + 46 + 'px'
        el.style.paddingTop = adminBarPosEl[i]['paddingTop'] + 'px'
      }
    } else {
      if (adminBarPosEl[i]['breakpoint'] !== 0) {
        adminBarPosEl[i]['breakpoint'] = 0
        el.style.marginTop = adminBarPosEl[i]['marginTop'] + 32 + 'px'
        el.style.paddingTop = adminBarPosEl[i]['paddingTop'] + 'px'
      }
    }
  }
}

window.addEventListener('load', () => {
  const elements = document.querySelectorAll('[data-control="admin-bar-pos"]')

  Array.from(elements).forEach((el, i) => {
    adminBarPosition(el, i)

    window.addEventListener('resize', () => {
      adminBarPosition(el, i)
    })

    window.addEventListener('scroll', () => {
      adminBarPosition(el, i)
    })
  })
})

