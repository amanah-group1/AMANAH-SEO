<?php defined( 'ABSPATH' ) || exit; ?>
<div class="wrap">
	<h1><?php esc_html_e( 'Authority & Author', 'amanah-seo-ai' ); ?></h1>
	<p><?php esc_html_e( 'Satu tempat untuk menyiapkan Expert Profile, WordPress Author dan Social Presence. Data Author dan Social Profile disimpan pada WordPress User agar tidak terjadi duplikasi.', 'amanah-seo-ai' ); ?></p>

	<?php if ( $message ) : ?>
		<div class="notice notice-info"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif; ?>

	<?php if ( empty( $entities ) ) : ?>
		<div class="notice notice-warning"><p><?php esc_html_e( 'Buat Entity Profile terlebih dahulu sebelum menyiapkan Expert dan Author.', 'amanah-seo-ai' ); ?></p></div>
	<?php else : ?>
		<div style="display:flex;justify-content:space-between;align-items:center;margin:20px 0 10px;">
			<h2 style="margin:0;"><?php esc_html_e( 'Author List', 'amanah-seo-ai' ); ?></h2>
			<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=amanah-authority&mode=add' ) ); ?>"><?php esc_html_e( '+ Tambah Author', 'amanah-seo-ai' ); ?></a>
		</div>
		<p class="description"><?php esc_html_e( 'Author adalah WordPress User. Hapus di sini hanya melepas hubungan Author dengan Entity; WordPress User, Author Profile dan Social Profile tetap aman.', 'amanah-seo-ai' ); ?></p>
		<table class="widefat striped">
			<thead><tr><th>Author</th><th>Entity</th><th>Aksi</th></tr></thead>
			<tbody>
			<?php if ( empty( $assignments ) ) : ?>
				<tr><td colspan="3"><?php esc_html_e( 'Belum ada Author yang dikonfigurasi.', 'amanah-seo-ai' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $assignments as $assignment ) : ?>
					<tr>
						<td><?php echo esc_html( $assignment['user']->display_name . ' — ' . $assignment['user']->user_email ); ?></td>
						<td><?php echo esc_html( $assignment['entity']->entity_name ); ?></td>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=amanah-authority&mode=edit&user_id=' . absint( $assignment['user']->ID ) . '&entity_id=' . absint( $assignment['entity']->id ) ) ); ?>">Edit</a>
							&nbsp;|&nbsp;
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline;">
								<input type="hidden" name="action" value="amanah_delete_authority">
								<input type="hidden" name="user_id" value="<?php echo absint( $assignment['user']->ID ); ?>">
								<input type="hidden" name="entity_id" value="<?php echo absint( $assignment['entity']->id ); ?>">
								<?php wp_nonce_field( 'amanah_delete_authority', 'amanah_authority_delete_nonce' ); ?>
								<button type="submit" class="button-link" onclick="return confirm('<?php echo esc_js( __( 'Lepaskan Author dari Entity ini? WordPress User dan Social Profile tidak akan dihapus.', 'amanah-seo-ai' ) ); ?>');">Hapus</button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>

		<?php $mode = isset( $_GET['mode'] ) ? sanitize_key( $_GET['mode'] ) : 'view'; ?>
		<?php if ( in_array( $mode, array( 'add', 'edit' ), true ) ) : ?>
			<h2><?php echo 'add' === $mode ? esc_html__( 'Tambah Author', 'amanah-seo-ai' ) : esc_html__( 'Edit Author', 'amanah-seo-ai' ); ?></h2>
			<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
				<input type="hidden" name="page" value="amanah-authority">
				<input type="hidden" name="mode" value="<?php echo esc_attr( $mode ); ?>">
				<table class="form-table">
					<tr><th><label for="amanah-authority-user">WordPress Author</label></th><td>
						<select name="user_id" id="amanah-authority-user">
						<?php foreach ( $users as $user ) : if ( ! $user ) { continue; } ?>
							<option value="<?php echo absint( $user->ID ); ?>" <?php selected( $user_id, $user->ID ); ?>><?php echo esc_html( $user->display_name . ' — ' . $user->user_email ); ?></option>
						<?php endforeach; ?>
						</select>
					</td></tr>
					<tr><th><label for="amanah-authority-entity">Entity</label></th><td>
						<select name="entity_id" id="amanah-authority-entity">
						<?php foreach ( $entities as $entity ) : ?>
						<option value="<?php echo absint( $entity->id ); ?>" <?php selected( $entity_id, $entity->id ); ?>><?php echo esc_html( $entity->entity_name ); ?></option>
						<?php endforeach; ?>
						</select>
						<p class="description"><?php esc_html_e( 'Expert Profile dapat berbeda untuk setiap Entity yang menggunakan WordPress Author yang sama.', 'amanah-seo-ai' ); ?></p>
					</td></tr>
				</table>
				<?php submit_button( __( 'Load Profile', 'amanah-seo-ai' ), 'secondary' ); ?>
			</form>

			<?php if ( $selected_user && $entity_id ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="amanah_save_authority">
				<input type="hidden" name="user_id" value="<?php echo absint( $user_id ); ?>">
				<input type="hidden" name="entity_id" value="<?php echo absint( $entity_id ); ?>">
				<?php wp_nonce_field( 'amanah_save_authority', 'amanah_authority_nonce' ); ?>
				<h2>Author Profile</h2>
				<p class="description"><?php esc_html_e( 'Data ini adalah data WordPress User. Saat disimpan di sini, data akan diperbarui pada profile WordPress user yang dipilih.', 'amanah-seo-ai' ); ?></p>
				<?php $profile_photo_id = isset( $author['profile_photo_id'] ) ? absint( $author['profile_photo_id'] ) : 0; ?>
				<?php $profile_photo_url = $profile_photo_id ? wp_get_attachment_image_url( $profile_photo_id, 'thumbnail' ) : ''; ?>
				<table class="form-table"><tr>
					<th><label for="amanah-profile-photo-id"><?php esc_html_e( 'Foto Profil Author', 'amanah-seo-ai' ); ?></label></th>
					<td>
						<input type="hidden" id="amanah-profile-photo-id" name="profile_photo_id" value="<?php echo esc_attr( $profile_photo_id ); ?>">
						<img id="amanah-profile-photo-preview" src="<?php echo $profile_photo_url ? esc_url( $profile_photo_url ) : ''; ?>" alt="" style="<?php echo $profile_photo_url ? 'display:block;' : 'display:none;'; ?>width:96px;height:96px;object-fit:cover;border-radius:50%;border:1px solid #ccd0d4;margin-bottom:10px;">
						<p><button type="button" class="button amanah-profile-photo-select"><?php echo $profile_photo_id ? esc_html__( 'Ganti Foto', 'amanah-seo-ai' ) : esc_html__( 'Upload / Pilih Foto', 'amanah-seo-ai' ); ?></button>
						<button type="button" class="button amanah-profile-photo-remove" style="<?php echo $profile_photo_id ? '' : 'display:none;'; ?>margin-left:6px;"><?php esc_html_e( 'Hapus Foto', 'amanah-seo-ai' ); ?></button></p>
						<p class="description"><?php esc_html_e( 'Pilih gambar dari WordPress Media Library. Foto ini akan digunakan oleh filter avatar WordPress.', 'amanah-seo-ai' ); ?></p>
					</td>
				</tr></table>
				<?php $author_fields = array(
					'display_name' => array( 'label' => 'Display Name', 'help' => 'Nama yang ingin ditampilkan sebagai penulis artikel.', 'function' => 'Menentukan nama Author yang tampil di WordPress dan dapat digunakan pada tampilan artikel.', 'placeholder' => 'Contoh: Budi Santoso', 'type' => 'text' ),
					'description' => array( 'label' => 'Description / Bio', 'help' => 'Tuliskan ringkasan singkat tentang Author dengan fakta yang benar.', 'function' => 'Memberikan konteks identitas Author untuk profil dan tampilan penulis.', 'placeholder' => 'Contoh: Praktisi SEO dan strategi digital yang berfokus pada pengembangan visibilitas organik.', 'type' => 'textarea' ),
					'website' => array( 'label' => 'Website', 'help' => 'Masukkan website pribadi atau website profesional Author jika tersedia.', 'function' => 'Menghubungkan profil Author dengan website yang relevan.', 'placeholder' => 'Opsional — contoh: https://contoh.com', 'type' => 'url' ),
				); foreach ( $author_fields as $key => $field ) : ?>
					<p><label for="amanah-author-<?php echo esc_attr($key); ?>"><strong><?php echo esc_html($field['label']); ?></strong></label><br>
					<span class="description" style="display:block;max-width:760px;margin:4px 0;"><?php echo esc_html($field['help']); ?></span>
					<?php if ( 'textarea' === $field['type'] ) : ?><textarea id="amanah-author-<?php echo esc_attr($key); ?>" class="large-text" rows="4" name="<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>"><?php echo esc_textarea($author[$key]); ?></textarea><?php else : ?><input id="amanah-author-<?php echo esc_attr($key); ?>" class="regular-text" type="<?php echo esc_attr($field['type']); ?>" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($author[$key]); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>"><?php endif; ?>
					<span class="description" style="display:block;max-width:760px;margin-top:4px;"><strong><?php esc_html_e('Fungsi:','amanah-seo-ai'); ?></strong> <?php echo esc_html($field['function']); ?></span></p>
				<?php endforeach; ?>

				<h2>Expert Profile</h2>
				<p class="description"><?php esc_html_e( 'Isi manual fakta keahlian, pengalaman dan kredensial. AI tidak akan mengarang data yang tidak Anda berikan.', 'amanah-seo-ai' ); ?></p>
				<?php $expert_fields = array(
					'expert_name'=>array('label'=>'Expert Name','help'=>'Masukkan nama profesional yang benar-benar digunakan oleh Expert.','function'=>'Menentukan identitas Expert yang terkait dengan Authority Profile.','placeholder'=>'Contoh: Budi Santoso','type'=>'text'),
					'role'=>array('label'=>'Role / Position','help'=>'Masukkan jabatan atau peran profesional yang benar-benar dimiliki.','function'=>'Menjelaskan posisi Expert dalam konteks profesional.','placeholder'=>'Contoh: SEO Consultant / SEO Strategist','type'=>'text'),
					'short_bio'=>array('label'=>'Short Biography','help'=>'Tulis ringkasan pengalaman dan keahlian dalam beberapa kalimat.','function'=>'Menjadi ringkasan singkat Expert untuk kebutuhan tampilan atau konteks konten.','placeholder'=>'Contoh: Praktisi SEO dengan pengalaman membantu bisnis membangun strategi organic growth.','type'=>'textarea'),
					'full_bio'=>array('label'=>'Full Biography','help'=>'Tulis profil profesional lebih lengkap berdasarkan fakta yang dapat diverifikasi.','function'=>'Memberikan konteks mendalam tentang latar belakang dan otoritas Expert.','placeholder'=>'Contoh: Jelaskan perjalanan profesional, fokus keahlian, pengalaman utama, dan bidang yang dikuasai.','type'=>'textarea'),
					'primary_expertise'=>array('label'=>'Primary Expertise','help'=>'Masukkan keahlian utama yang paling merepresentasikan Expert.','function'=>'Menentukan fokus keahlian utama yang dapat digunakan sebagai konteks Authority.','placeholder'=>'Contoh: SEO Strategy, Technical SEO, Content Strategy','type'=>'textarea'),
					'secondary_expertise'=>array('label'=>'Secondary Expertise','help'=>'Masukkan keahlian pendukung yang memang dikuasai.','function'=>'Memperluas konteks keahlian tanpa mengubah fokus utama Expert.','placeholder'=>'Contoh: Digital Marketing, Analytics, AI Visibility','type'=>'textarea'),
					'topics'=>array('label'=>'Topics of Expertise','help'=>'Tuliskan topik yang benar-benar sering Anda bahas atau kuasai.','function'=>'Membantu menghubungkan Expert dengan topik konten yang relevan.','placeholder'=>'Contoh: SEO, AI, Content Marketing, Search Visibility','type'=>'textarea'),
					'industries'=>array('label'=>'Industries','help'=>'Tuliskan industri yang memang pernah Anda tangani atau kuasai.','function'=>'Memberikan konteks industri untuk Authority dan konten.','placeholder'=>'Contoh: Technology, E-commerce, Digital Business','type'=>'textarea'),
					'years_experience'=>array('label'=>'Years of Experience','help'=>'Masukkan jumlah tahun pengalaman yang benar-benar dimiliki.','function'=>'Memberikan sinyal pengalaman profesional berdasarkan data manual.','placeholder'=>'Contoh: 10','type'=>'text'),
					'experience'=>array('label'=>'Professional Experience','help'=>'Tuliskan pengalaman kerja, proyek, peran, atau pencapaian profesional yang relevan.','function'=>'Mendokumentasikan bukti pengalaman Expert secara terstruktur.','placeholder'=>'Contoh: SEO Lead di ..., menangani strategi organic growth untuk ...','type'=>'textarea'),
					'education'=>array('label'=>'Education','help'=>'Masukkan pendidikan formal atau pelatihan relevan yang benar-benar pernah ditempuh.','function'=>'Memberikan konteks latar belakang pendidikan Expert.','placeholder'=>'Contoh: S1 Teknik Informatika — Universitas ...','type'=>'textarea'),
					'certifications'=>array('label'=>'Certifications','help'=>'Masukkan sertifikasi profesional yang benar-benar dimiliki.','function'=>'Mendokumentasikan sertifikasi yang dapat mendukung kredibilitas Expert.','placeholder'=>'Contoh: Google Analytics Certification, HubSpot SEO Certification','type'=>'textarea'),
					'credentials'=>array('label'=>'Professional Credentials','help'=>'Masukkan kredensial profesional lain yang dapat dibuktikan.','function'=>'Menyimpan bukti kredensial tambahan di luar pendidikan dan sertifikasi.','placeholder'=>'Contoh: Member organisasi profesi, speaker, trainer, atau credential profesional lainnya.','type'=>'textarea'),
					'awards'=>array('label'=>'Awards / Recognition','help'=>'Masukkan penghargaan atau pengakuan profesional yang benar-benar diterima.','function'=>'Mendokumentasikan recognition yang dapat mendukung Authority Profile.','placeholder'=>'Opsional — contoh: Best SEO Campaign 2025','type'=>'textarea'),
				); foreach ( $expert_fields as $key => $field ) : ?>
					<p><label for="amanah-expert-<?php echo esc_attr($key); ?>"><strong><?php echo esc_html($field['label']); ?></strong></label><br>
					<span class="description" style="display:block;max-width:760px;margin:4px 0;"><?php echo esc_html($field['help']); ?></span>
					<?php if ( 'textarea' === $field['type'] ) : ?><textarea id="amanah-expert-<?php echo esc_attr($key); ?>" class="large-text" rows="4" name="<?php echo esc_attr($key); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>"><?php echo esc_textarea($expert[$key]); ?></textarea><?php else : ?><input id="amanah-expert-<?php echo esc_attr($key); ?>" class="regular-text" type="text" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($expert[$key]); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>"><?php endif; ?>
					<span class="description" style="display:block;max-width:760px;margin-top:4px;"><strong><?php esc_html_e('Fungsi:','amanah-seo-ai'); ?></strong> <?php echo esc_html($field['function']); ?></span></p>
				<?php endforeach; ?>

				<h2>Social Presence</h2>
				<p class="description"><?php esc_html_e( 'Isi platform yang memang digunakan. Data ini disimpan pada WordPress User Meta dan juga ditampilkan di profile WordPress, sehingga tidak ada data social profile kedua yang terpisah.', 'amanah-seo-ai' ); ?></p>
				<p class="description" style="max-width:760px;"><strong><?php esc_html_e( 'Rekomendasi:', 'amanah-seo-ai' ); ?></strong> <?php esc_html_e( 'Usahakan menggunakan profil resmi dan terverifikasi (centang biru) apabila fitur verifikasi tersedia pada platform tersebut. Verifikasi dapat membantu memperkuat kredibilitas identitas dan kepercayaan terhadap profil, tetapi bukan merupakan persyaratan wajib.', 'amanah-seo-ai' ); ?></p>
				<?php $social_fields = array(
					'linkedin'=>array('label'=>'LinkedIn','help'=>'Masukkan profil LinkedIn resmi Author jika memang digunakan.','function'=>'Menghubungkan Author dengan profil profesional di LinkedIn.','placeholder'=>'Opsional — contoh: https://www.linkedin.com/in/nama'),
					'facebook'=>array('label'=>'Facebook','help'=>'Masukkan profil atau halaman Facebook yang benar-benar digunakan oleh Author.','function'=>'Menghubungkan Author dengan kehadiran sosial di Facebook.','placeholder'=>'Opsional — contoh: https://www.facebook.com/nama'),
					'instagram'=>array('label'=>'Instagram','help'=>'Masukkan profil Instagram resmi Author jika memang digunakan.','function'=>'Menghubungkan Author dengan profil sosial di Instagram.','placeholder'=>'Opsional — contoh: https://www.instagram.com/nama'),
					'youtube'=>array('label'=>'YouTube','help'=>'Masukkan channel YouTube resmi Author jika memang digunakan.','function'=>'Menghubungkan Author dengan channel video yang relevan.','placeholder'=>'Opsional — contoh: https://www.youtube.com/@nama'),
					'x'=>array('label'=>'X','help'=>'Masukkan profil X resmi Author jika memang digunakan.','function'=>'Menghubungkan Author dengan profil sosial di X.','placeholder'=>'Opsional — contoh: https://x.com/nama'),
					'telegram'=>array('label'=>'Telegram','help'=>'Masukkan username atau profil Telegram yang memang digunakan untuk publik.','function'=>'Menghubungkan Author dengan kanal atau profil Telegram yang relevan.','placeholder'=>'Opsional — contoh: https://t.me/nama'),
					'whatsapp'=>array('label'=>'WhatsApp','help'=>'Masukkan tautan WhatsApp publik jika memang digunakan.','function'=>'Menghubungkan Author dengan kanal kontak WhatsApp yang relevan.','placeholder'=>'Opsional — contoh: https://wa.me/628123456789'),
				); foreach ( $social_fields as $key => $field ) : ?>
					<p><label for="amanah-social-<?php echo esc_attr($key); ?>"><strong><?php echo esc_html($field['label']); ?></strong></label><br>
					<span class="description" style="display:block;max-width:760px;margin:4px 0;"><?php echo esc_html($field['help']); ?></span>
					<input id="amanah-social-<?php echo esc_attr($key); ?>" class="large-text" type="url" name="social_<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($social[$key]); ?>" placeholder="<?php echo esc_attr($field['placeholder']); ?>">
					<span class="description" style="display:block;max-width:760px;margin-top:4px;"><strong><?php esc_html_e('Fungsi:','amanah-seo-ai'); ?></strong> <?php echo esc_html($field['function']); ?></span></p>
				<?php endforeach; ?>
				<?php submit_button( __( 'Save & Synchronize with WordPress', 'amanah-seo-ai' ) ); ?>
			</form>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
</div>