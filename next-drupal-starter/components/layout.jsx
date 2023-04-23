import { Footer, Header, PreviewRibbon } from '@pantheon-systems/nextjs-kit';
import styles from './layout.module.css';

export default function Layout({ children, footerMenu, preview = false }) {
	const navItems = [
		{ linkText: '🏠 Home', href: '/' },
		{ linkText: '📰 Articles', href: '/articles' },
		{ linkText: '📑 Pages', href: '/pages' },
		{ linkText: '⚛️ Examples', href: '/examples' },
	];
	const footerMenuItems = footerMenu?.map(({ title, url, parent }) => ({
		linkText: title,
		href: url,
		parent: parent,
	}));

	return (
		<div className={styles.layout}>
			{preview && <PreviewRibbon />}
			<Header navItems={navItems} />
			<main className={styles.main}>{children}</main>
			<Footer footerMenuItems={footerMenuItems}>
				<span className={styles.footerCopy}>
					© {new Date().getFullYear()} Built with{' '}
					<a
						href="https://nextjs.org/"
					>
						Next.js
					</a>{' '}
					and{' '}
					<a
						href="https://www.drupal.org/"
					>
						Drupal
					</a>
				</span>
			</Footer>
		</div>
	);
}
