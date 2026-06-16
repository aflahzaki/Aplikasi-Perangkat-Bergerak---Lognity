import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';
import '../theme/app_theme.dart';
import '../widgets/lognity_logo.dart';

class LoginScreen extends StatefulWidget {
  @override
  _LoginScreenState createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> with SingleTickerProviderStateMixin {
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isLoading = false;
  bool _obscurePassword = true;
  late AnimationController _animController;
  late Animation<double> _fadeAnim;

  @override
  void initState() {
    super.initState();
    _animController = AnimationController(vsync: this, duration: const Duration(milliseconds: 800));
    _fadeAnim = CurvedAnimation(parent: _animController, curve: Curves.easeOut);
    _animController.forward();
  }

  @override
  void dispose() {
    _animController.dispose();
    super.dispose();
  }

  void _login() async {
    setState(() { _isLoading = true; });

    try {
      final response = await ApiService.login(
        _emailController.text, 
        _passwordController.text
      );

      if (response.containsKey('token')) {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('token', response['token']);
        
        Navigator.of(context).pushReplacementNamed('/main');
      } else {
        _showError(response['message'] ?? 'Login gagal. Coba cek email/password Anda.');
      }
    } catch (e) {
      _showError('Terjadi kesalahan koneksi. Pastikan server Laravel aktif.');
    } finally {
      setState(() { _isLoading = false; });
    }
  }

  void _showError(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: Colors.red.shade400,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(gradient: AppTheme.heroGradient),
        child: Stack(
          children: [
            // Decorative blobs
            Positioned(top: -60, right: -60, child: Container(
              width: 200, height: 200,
              decoration: BoxDecoration(color: Colors.white.withOpacity(0.08), shape: BoxShape.circle),
            )),
            Positioned(bottom: -80, left: -40, child: Container(
              width: 250, height: 250,
              decoration: BoxDecoration(color: AppTheme.funYellow.withOpacity(0.12), shape: BoxShape.circle),
            )),
            Positioned(top: MediaQuery.of(context).size.height * 0.3, left: -100, child: Container(
              width: 200, height: 200,
              decoration: BoxDecoration(color: AppTheme.funPink.withOpacity(0.1), shape: BoxShape.circle),
            )),

            // Content
            Center(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(24),
                child: FadeTransition(
                  opacity: _fadeAnim,
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      // Logo
                      Container(
                        padding: const EdgeInsets.all(4),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.15),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: Colors.white.withOpacity(0.2)),
                        ),
                        child: const LognityLogo(size: 64, fontSize: 36),
                      ),
                      const SizedBox(height: 20),
                      Text('Lognity', style: GoogleFonts.poppins(
                        fontSize: 36, fontWeight: FontWeight.bold, color: Colors.white,
                        letterSpacing: -0.5,
                      )),
                      const SizedBox(height: 4),
                      Text('E-Library & Forum Kampus', style: GoogleFonts.poppins(
                        fontSize: 14, color: Colors.white70,
                      )),
                      const SizedBox(height: 40),

                      // Glassmorphism Card
                      Container(
                        constraints: const BoxConstraints(maxWidth: 400),
                        padding: const EdgeInsets.all(28),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.12),
                          borderRadius: BorderRadius.circular(28),
                          border: Border.all(color: Colors.white.withOpacity(0.2)),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            Text('Masuk ke Akunmu', style: GoogleFonts.poppins(
                              fontSize: 20, fontWeight: FontWeight.w600, color: Colors.white,
                            )),
                            const SizedBox(height: 24),
                            TextField(
                              controller: _emailController,
                              style: const TextStyle(color: Colors.white),
                              decoration: InputDecoration(
                                hintText: 'Email',
                                hintStyle: TextStyle(color: Colors.white54),
                                prefixIcon: const Icon(Icons.email_outlined, color: Colors.white70),
                                filled: true,
                                fillColor: Colors.white.withOpacity(0.1),
                                border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide.none),
                                enabledBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(14),
                                  borderSide: BorderSide(color: Colors.white.withOpacity(0.2)),
                                ),
                                focusedBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(14),
                                  borderSide: const BorderSide(color: AppTheme.funYellow, width: 2),
                                ),
                              ),
                            ),
                            const SizedBox(height: 16),
                            TextField(
                              controller: _passwordController,
                              obscureText: _obscurePassword,
                              style: const TextStyle(color: Colors.white),
                              decoration: InputDecoration(
                                hintText: 'Password',
                                hintStyle: TextStyle(color: Colors.white54),
                                prefixIcon: const Icon(Icons.lock_outline, color: Colors.white70),
                                suffixIcon: IconButton(
                                  icon: Icon(_obscurePassword ? Icons.visibility_off : Icons.visibility, color: Colors.white54),
                                  onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                                ),
                                filled: true,
                                fillColor: Colors.white.withOpacity(0.1),
                                border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide.none),
                                enabledBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(14),
                                  borderSide: BorderSide(color: Colors.white.withOpacity(0.2)),
                                ),
                                focusedBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(14),
                                  borderSide: const BorderSide(color: AppTheme.funYellow, width: 2),
                                ),
                              ),
                            ),
                            const SizedBox(height: 28),
                            _isLoading
                              ? const Center(child: CircularProgressIndicator(color: Colors.white))
                              : Container(
                                  height: 52,
                                  decoration: BoxDecoration(
                                    gradient: const LinearGradient(colors: [AppTheme.funYellow, Color(0xFFfbbf24)]),
                                    borderRadius: BorderRadius.circular(14),
                                    boxShadow: [BoxShadow(color: AppTheme.funYellow.withOpacity(0.3), blurRadius: 12, offset: const Offset(0, 4))],
                                  ),
                                  child: ElevatedButton(
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: Colors.transparent,
                                      shadowColor: Colors.transparent,
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                                    ),
                                    onPressed: _login,
                                    child: Text('Masuk', style: GoogleFonts.poppins(
                                      fontSize: 16, fontWeight: FontWeight.bold, color: const Color(0xFF1e293b),
                                    )),
                                  ),
                                ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 20),
                      TextButton(
                        onPressed: () => Navigator.pushNamed(context, '/register'),
                        child: RichText(text: TextSpan(
                          style: GoogleFonts.poppins(fontSize: 14, color: Colors.white70),
                          children: const [
                            TextSpan(text: 'Belum punya akun? '),
                            TextSpan(text: 'Daftar sekarang', style: TextStyle(color: AppTheme.funYellow, fontWeight: FontWeight.bold)),
                          ],
                        )),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
