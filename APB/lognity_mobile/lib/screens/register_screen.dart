import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';
import '../theme/app_theme.dart';
import '../widgets/lognity_logo.dart';

class RegisterScreen extends StatefulWidget {
  @override
  _RegisterScreenState createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> with SingleTickerProviderStateMixin {
  final _usernameController = TextEditingController();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _passwordConfirmController = TextEditingController();
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

  void _register() async {
    if (_passwordController.text != _passwordConfirmController.text) {
      _showError('Konfirmasi password tidak cocok');
      return;
    }

    setState(() { _isLoading = true; });

    try {
      final response = await ApiService.register(
        _usernameController.text,
        _emailController.text, 
        _passwordController.text
      );

      if (response.containsKey('token')) {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('token', response['token']);
        Navigator.of(context).pushReplacementNamed('/main');
      } else {
        _showError(response['message'] ?? 'Registrasi gagal');
      }
    } catch (e) {
      _showError('Terjadi kesalahan koneksi');
    } finally {
      setState(() { _isLoading = false; });
    }
  }

  void _showError(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: Colors.red.shade400,
        behavior: SnackBarBehavior.floating, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))),
    );
  }

  Widget _buildField(TextEditingController controller, String hint, IconData icon, {bool isPassword = false}) {
    return TextField(
      controller: controller,
      obscureText: isPassword ? _obscurePassword : false,
      style: const TextStyle(color: Colors.white),
      decoration: InputDecoration(
        hintText: hint,
        hintStyle: TextStyle(color: Colors.white54),
        prefixIcon: Icon(icon, color: Colors.white70),
        suffixIcon: isPassword ? IconButton(
          icon: Icon(_obscurePassword ? Icons.visibility_off : Icons.visibility, color: Colors.white54),
          onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
        ) : null,
        filled: true,
        fillColor: Colors.white.withOpacity(0.1),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide.none),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide(color: Colors.white.withOpacity(0.2))),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: const BorderSide(color: AppTheme.funYellow, width: 2)),
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
            Positioned(top: -60, left: -60, child: Container(width: 200, height: 200, decoration: BoxDecoration(color: Colors.white.withOpacity(0.08), shape: BoxShape.circle))),
            Positioned(bottom: -80, right: -40, child: Container(width: 250, height: 250, decoration: BoxDecoration(color: AppTheme.funPink.withOpacity(0.12), shape: BoxShape.circle))),
            
            Center(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(24),
                child: FadeTransition(
                  opacity: _fadeAnim,
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Container(
                        padding: const EdgeInsets.all(4),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.15),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: Colors.white.withOpacity(0.2)),
                        ),
                        child: const LognityLogo(size: 56, fontSize: 32),
                      ),
                      const SizedBox(height: 16),
                      Text('Buat Akun Lognity', style: GoogleFonts.poppins(fontSize: 28, fontWeight: FontWeight.bold, color: Colors.white)),
                      const SizedBox(height: 4),
                      Text('Daftar untuk mengakses Forum dan E-Library', style: GoogleFonts.poppins(fontSize: 13, color: Colors.white70)),
                      const SizedBox(height: 32),

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
                            _buildField(_usernameController, 'Username', Icons.person_outline),
                            const SizedBox(height: 14),
                            _buildField(_emailController, 'Email', Icons.email_outlined),
                            const SizedBox(height: 14),
                            _buildField(_passwordController, 'Password', Icons.lock_outline, isPassword: true),
                            const SizedBox(height: 14),
                            TextField(
                              controller: _passwordConfirmController,
                              obscureText: true,
                              style: const TextStyle(color: Colors.white),
                              decoration: InputDecoration(
                                hintText: 'Konfirmasi Password',
                                hintStyle: TextStyle(color: Colors.white54),
                                prefixIcon: const Icon(Icons.lock_outline, color: Colors.white70),
                                filled: true,
                                fillColor: Colors.white.withOpacity(0.1),
                                border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide.none),
                                enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide(color: Colors.white.withOpacity(0.2))),
                                focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: const BorderSide(color: AppTheme.funYellow, width: 2)),
                              ),
                            ),
                            const SizedBox(height: 24),
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
                                    style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14))),
                                    onPressed: _register,
                                    child: Text('Daftar', style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.bold, color: const Color(0xFF1e293b))),
                                  ),
                                ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 20),
                      TextButton(
                        onPressed: () => Navigator.of(context).pop(),
                        child: RichText(text: TextSpan(
                          style: GoogleFonts.poppins(fontSize: 14, color: Colors.white70),
                          children: const [
                            TextSpan(text: 'Sudah punya akun? '),
                            TextSpan(text: 'Masuk di sini', style: TextStyle(color: AppTheme.funYellow, fontWeight: FontWeight.bold)),
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
