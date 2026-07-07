# Hostinger Deployment Checklist

1. Point `Limbe.Net` DNS to Hostinger nameservers or the hosting IP.
2. Install WordPress through Hostinger hPanel.
3. Enable SSL before public launch.
4. Create a staging site first.
5. Upload `dist/limbenet.zip` through `Appearance > Themes > Add New`, or upload `wp-content/themes/limbenet/` by SFTP to `/wp-content/themes/limbenet/`.
6. Upload `dist/limbenet-core.zip` through `Plugins > Add New`, or upload `wp-content/plugins/limbenet-core/` by SFTP to `/wp-content/plugins/limbenet-core/`.
7. Activate the `Limbe.Net` theme.
8. Activate the `Limbe.Net Core` plugin.
9. Install Polylang or WPML.
10. Configure English as default, then add French and Spanish.
11. Configure language URLs as `/en/`, `/fr/`, and `/es/`.
12. Set permalinks to `Post name`.
13. Install LiteSpeed Cache.
14. Install Rank Math or Yoast SEO.
15. Install a security plugin and enable two-factor authentication for admins if available.
16. Configure `Limbe.Net Tourism > Settings`.
17. Import starter content from `Limbe.Net Tourism > Seed Importer`.
18. Review every seed attraction before public launch.
19. Replace placeholder safety notes with verified, current planning language.
20. Replace `Price not yet verified.` only when a reliable source is available.
21. Test partner listing, booking help, claim listing, and advertising forms.
22. Test search filters for region, city, attraction type, safety status, family friendly, and ticket required.
23. Test mobile layout on homepage, attraction archive, attraction single, and partner form pages.
24. Configure caching after content and translation setup.
25. Generate and submit XML sitemaps from Rank Math or Yoast.
26. Push staging to live.
