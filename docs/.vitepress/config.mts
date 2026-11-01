import { defineConfig } from 'vitepress'

export default defineConfig({
  base: process.env.GITHUB_ACTIONS ? '/basalam-laravel/' : '/',
  title: 'Basalam Laravel',
  description: 'Robust, TDD-backed Laravel package for Basalam API Gateway and Core with Interface Contracts and Zero SDK bloat',
  lang: 'en-US',
  lastUpdated: true,
  themeConfig: {
    nav: [
      { text: 'Guide', link: '/guide/getting-started' },
      { text: 'Contracts & DTOs', link: '/guide/architecture-contracts' },
      { text: 'API Services', link: '/guide/products' },
      { text: 'GitHub', link: 'https://github.com/eamirgh/basalam-laravel' }
    ],
    sidebar: [
      {
        text: 'Getting Started',
        items: [
          { text: 'Overview & Installation', link: '/guide/getting-started' },
          { text: 'Configuration', link: '/guide/configuration' },
          { text: 'Interface Contracts & DTOs', link: '/guide/architecture-contracts' },
          { text: 'Authentication & OAuth 2.0', link: '/guide/authentication' }
        ]
      },
      {
        text: 'Features & Services',
        items: [
          { text: 'Products & Variations', link: '/guide/products' },
          { text: 'Cart & Baskets', link: '/guide/cart-basket' },
          { text: 'Orders & Parcels', link: '/guide/orders-parcels' },
          { text: 'Vendors & Core Service', link: '/guide/vendors' },
          { text: 'File Uploads', link: '/guide/files' },
          { text: 'Webhooks', link: '/guide/webhooks' }
        ]
      },
      {
        text: 'Best Practices',
        items: [
          { text: 'Testing & Mocking', link: '/guide/testing' },
          { text: 'Troubleshooting & FAQ', link: '/guide/troubleshooting' }
        ]
      }
    ],
    socialLinks: [
      { icon: 'github', link: 'https://github.com/eamirgh/basalam-laravel' }
    ],
    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2026 Amir Ghaffari (eamirgh)'
    },
    search: {
      provider: 'local'
    }
  }
})
